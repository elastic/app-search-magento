<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;

/**
 * Extract and build filters from the search request.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var QueryFilterBuilderInterfaceFactory
     */
    private $queryFilterBuilderFactory;

    /**
     * @var array
     */
    private $queryFilterBuilders = [];

    /**
     * @var FieldMapperResolverInterface
     */
    private $fieldMapperResolver;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param QueryFilterBuilderInterfaceFactory $queryFilterBuilderFactory
     * @param FieldMapperResolverInterface       $fieldMapperResolver
     */
    public function __construct(
        QueryFilterBuilderInterfaceFactory $queryFilterBuilderFactory,
        FieldMapperResolverInterface $fieldMapperResolver
    ) {
        $this->queryFilterBuilderFactory = $queryFilterBuilderFactory;
        $this->fieldMapperResolver       = $fieldMapperResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $filters = $request->getQuery() ? $this->getQueryFilterBuilder($request)->getFilter($request->getQuery()) : [];

        return !empty($filters) ? ['filters' => $filters] : [];
    }

    /**
     * Return query filter builder for the current request.
     *
     * @param RequestInterface $request
     *
     * @return QueryFilterBuilderInterface
     */
    private function getQueryFilterBuilder(RequestInterface $request): QueryFilterBuilderInterface
    {
        $indexIdentifier = $request->getIndex();

        if (!isset($this->queryFilterBuilders[$indexIdentifier])) {
            $fieldMapper = $this->fieldMapperResolver->getFieldMapper($indexIdentifier);
            $queryFilterBuilder = $this->queryFilterBuilderFactory->create(['fieldMapper' => $fieldMapper]);
            $this->queryFilterBuilders[$indexIdentifier] = $queryFilterBuilder;
        }

        return $this->queryFilterBuilders[$indexIdentifier];
    }
}
