<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Search\Request\Builder;

use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Query\FilterFactory as FilteredQueryFactory;
use Magento\Framework\Search\Request\Filter\BoolExpressionFactory as BoolFilterFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Search\Request\Query\Filter;

/**
 * Build a query to represent filter groups.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Search\Request\Builder
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FilterGroupBuilder
{
    /**
     * @var FilteredQueryFactory
     */
    private $filteredQueryFactory;

    /**
     * @var BoolFilterFactory
     */
    private $boolFilterFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * Constructor.
     *
     * @param FilteredQueryFactory $filteredQueryFactory
     * @param BoolFilterFactory    $boolFilterFactory
     * @param FilterBuilder        $filterBuilder
     */
    public function __construct(
        FilteredQueryFactory $filteredQueryFactory,
        BoolFilterFactory $boolFilterFactory,
        FilterBuilder $filterBuilder
    ) {
        $this->filteredQueryFactory = $filteredQueryFactory;
        $this->boolFilterFactory    = $boolFilterFactory;
        $this->filterBuilder        = $filterBuilder;
    }

    /**
     * Generate the query that represent an array of filter groups.
     *
     * @param FilterGroup[] $filterGroups
     *
     * @return QueryInterface
     */
    public function create(array $filterGroups): QueryInterface
    {
        $queryParams = ['name' => '', 'boost' => 1, 'referenceType' => Filter::REFERENCE_FILTER];
        $filters     = [];

        foreach ($filterGroups as $filterGroup) {
            $filters[] = $this->createFilterGroupFilter($filterGroup);
        }

        $queryParams['reference'] = $this->wrapFilters($filters);

        return $this->filteredQueryFactory->create($queryParams);
    }

    /**
     * Generate filter for a filter group.
     *
     * @param FilterGroup $filterGroup
     *
     * @return FilterInterface
     */
    private function createFilterGroupFilter(FilterGroup $filterGroup): FilterInterface
    {
        $filters = [];

        foreach ($filterGroup->getFilters() as $filter) {
            $filters[] = $this->filterBuilder->create($filter);
        }

        return $this->wrapFilters($filters, 'should');
    }

    /**
     * Wrap an array of filters into a boolean query.
     *
     * @param FilterInterface[] $filters
     * @param string            $clause
     *
     * @return FilterInterface
     */
    private function wrapFilters(array $filters, string $clause = 'must'): FilterInterface
    {
        if (count($filters) == 1) {
            return current($filters);
        }

        return $this->boolFilterFactory->create(['name' => '', $clause => $filters]);
    }
}
