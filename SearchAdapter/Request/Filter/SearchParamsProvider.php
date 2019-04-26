<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Filter;

use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;

/**
 * Extract and build filters from the search request.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var QueryFilterBuilderInterface
     */
    private $queryFilterBuilder;

    /**
     * Constructor.
     *
     * @param QueryFilterBuilderInterface $queryFilterBuilder
     */
    public function __construct(QueryFilterBuilderInterface $queryFilterBuilder)
    {
        $this->queryFilterBuilder = $queryFilterBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $searchParams = [];

        $defaultFilters = ['all' => $this->getDefaultFilters()];
        $filters        = $request->getQuery() ? $this->queryFilterBuilder->getFilter($request->getQuery()) : [];

        if (!empty($filters) && empty(array_intersect(array_keys($filters), ["all", "any", "not"]))) {
            $filters = ["all" => $filters];
        }

        $searchParams['filters'] = array_merge_recursive($defaultFilters, $filters);

        return $searchParams;
    }

    /**
     * Return default filters used to unslice the catalog.
     *
     * @deprecated
     *
     * @return array
     */
    private function getDefaultFilters(): array
    {
        return [
            ['customer_group_id' => (string) $this->getCustomerGroupId()],
            ['category_id'       => (string) $this->getCategoryId()],
        ];
    }

    /**
     * @deprecated
     *
     * @return int
     */
    private function getCategoryId(): string
    {
        return 2;
    }

    /**
     * @deprecated
     *
     * @return int
     */
    private function getCustomerGroupId(): int
    {
        return 0;
    }
}
