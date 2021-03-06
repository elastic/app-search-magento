<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Search\Request;

use Elastic\AppSearch\Framework\Search\Request;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Search\Request\QueryInterface;
use Elastic\AppSearch\Framework\Search\Request\Builder\FilterGroupBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\Request\Query\BoolExpressionFactory as BoolQueryFactory;
use Magento\Framework\Search\Request\Cleaner;
use Magento\Framework\Search\Request\Binder;
use Magento\Framework\Search\Request\Config;

/**
 * AppSearch search request builder: append sort support to the original builder.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\Framework\Search\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Builder extends \Magento\Framework\Search\Request\Builder
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Api\SortOrder[]
     */
    private $sort;

    /**
     * @var FilterGroup[]
     */
    private $filterGroups = [];

    /**
     * @var string
     */
    private $requestName;

    /**
     * Connstructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Config                 $config
     * @param Binder                 $binder
     * @param Cleaner                $cleaner
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config,
        Binder $binder,
        Cleaner $cleaner
    ) {
        parent::__construct($objectManager, $config, $binder, $cleaner);

        $this->objectManager = $objectManager;
        $this->config        = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $request = parent::create();

        $requestData = [
            'name'       => $request->getName(),
            'indexName'  => $request->getIndex(),
            'query'      => $this->addFilterGroupsToQuery($request->getQuery()),
            'from'       => $request->getFrom(),
            'size'       => $request->getSize(),
            'dimensions' => $request->getDimensions(),
            'buckets'    => $request->getAggregation(),
        ];

        if (!empty($this->sort)) {
            $requestData['sort'] = $this->sort;
            $this->sort = [];
        }

        return $this->objectManager->create(Request::class, $requestData);
    }

    /**
     * {@inheritDoc}
     */
    public function setRequestName($requestName)
    {
        $this->requestName = $requestName;
        return parent::setRequestName($requestName);
    }

    /**
    * Set sort order for the request.
    *
    * @param \Magento\Framework\Api\SortOrder[] $sort
    *
    * @return $this
    */
    public function setSort($sort)
    {
        $this->sort = array_filter($sort, function ($sortOrder) {
            return $sortOrder->getField() && $sortOrder->getDirection();
        });

        return $this;
    }

    /**
     * Add a new filter group to the built request.
     *
     * @param FilterGroup $filterGroup
     *
     * @return $this
     */
    public function addFilterGroup(FilterGroup $filterGroup)
    {
        $filters = $filterGroup->getFilters();

        if (count($filters) == 1 && $this->canBind(current($filters))) {
            $filter = current($filters);
            $this->bind($this->getFieldName($filter), $filter->getValue());

            return $this;
        }

        $this->filterGroups[] = $filterGroup;

        return $this;
    }

    /**
     * Check if the current filter can use standard params binding.
     *
     * @param Filter $filter
     *
     * @return boolean
     */
    private function canBind(Filter $filter): bool
    {
        return in_array($this->getFieldName($filter), $this->getPlaceholders($this->requestName));
    }

    /**
     * Fix field name for a filter field.
     *
     * TODO: remove since hardcoded.
     *
     * @param Filter $filter
     *
     * @return string
     */
    private function getFieldName(Filter $filter): string
    {
        return $filter->getField() == 'category_id' ? 'category_ids' : $filter->getField();
    }

    /**
     * Fix the query generated by the base builder to add filter group to it.
     * @param QueryInterface $query
     *
     * @return QueryInterface
     */
    private function addFilterGroupsToQuery(QueryInterface $query): QueryInterface
    {
        if (!empty($this->filterGroups)) {
            $filterGroupQuery = $this->getFilterGroupBuilder()->create($this->filterGroups);
            $queryParams = ['name' => '', 'boost' => 1, 'must' => [$query, $filterGroupQuery]];
            $query = $this->getBoolQueryFactory()->create($queryParams);
            $this->filterGroups = [];
        }

        return $query;
    }

    /**
     * Retrive filter group query builder.
     *
     * @return FilterGroupBuilder
     */
    private function getFilterGroupBuilder(): FilterGroupBuilder
    {
        return $this->objectManager->get(FilterGroupBuilder::class);
    }

    /**
     * Boolean query factory.
     *
     * @return BoolQueryFactory
     */
    private function getBoolQueryFactory(): BoolQueryFactory
    {
        return $this->objectManager->get(BoolQueryFactory::class);
    }

    /**
     * Return list of placeholders for the current request.
     *
     * @param string $requestName
     *
     * @return string[]
     */
    private function getPlaceholders(string $requestName)
    {
        $config = $this->config->get($requestName);

        return $this->extractPlaceholders($config);
    }

    /**
     * Extract placeholders from the request config.
     *
     * @param array $config
     *
     * @return string[]
     */
    private function extractPlaceholders(array $config)
    {
        $placeholders = [];

        foreach ($config as $configValue) {
            if (is_array($configValue)) {
                $placeholders = array_merge($placeholders, $this->extractPlaceholders($configValue));
            } elseif (preg_match('/[$](.*)[$]$/', $configValue)) {
                $placeholders[] = trim($configValue, '$');
            }
        }

        return $placeholders;
    }
}
