<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\QueryFilter;

use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\QueryFilterBuilderInterface;
use Magento\Framework\Search\Request\QueryInterface;

/**
 * Implementation of the boolean query filter builder.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\QueryFilter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class BoolQueryFilterBuilder implements QueryFilterBuilderInterface
{
    /**
     * @var QueryFilterBuilderInterface
     */
    private $queryBuilder;

    /**
     * @var array
     */
    private $clauseMapping = [
        'all'  => 'getMust',
        'any'  => 'getShould',
        'none' => 'getMustNot',
    ];

    /**
     * Constructor.
     *
     * @param QueryFilterBuilderInterface $queryBuilder
     */
    public function __construct(QueryFilterBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter(QueryInterface $query): array
    {
        $filters = [];

        foreach ($this->clauseMapping as $clause => $method) {
            $subQueries = $this->getFilters($query->$method());
            if (!empty($subQueries)) {
                $filters[$clause] = $subQueries;
            }
        }

        return $this->simplifyFilter($filters);
    }

    /**
     * Build all queries contained into an array.
     *
     * @param QueryInterface[] $queries
     *
     * @return array
     */
    private function getFilters(array $queries)
    {
        $filters = array_map([$this->queryBuilder, 'getFilter'], $queries);

        return array_filter(array_values($filters));
    }

    /**
     * Try to simplify boolean clause if possible.
     *
     * @param array $filter
     *
     * @return array
     */
    private function simplifyFilter(array $filter)
    {
        if (count($filter) == 1 && !empty(array_intersect(array_keys($filter), ['any', 'all']))) {
            $filter = count(current($filter)) == 1 ? current(current($filter)) : $filter;
        } elseif (isset($filter['any']) && count($filter['any']) == 1) {
            $filter['all'][] = current($filter['any']);
            unset($filter['any']);
        }

        return $filter;
    }
}
