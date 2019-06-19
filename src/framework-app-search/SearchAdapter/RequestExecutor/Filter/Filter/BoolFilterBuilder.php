<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\Filter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\FilterBuilderInterface;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Implementation of the bool filter builder.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class BoolFilterBuilder implements FilterBuilderInterface
{
    /**
     * @var FilterBuilderInterface
     */
    private $filterBuilder;

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
     * @param FilterBuilderInterface $filterBuilder
     */
    public function __construct(FilterBuilderInterface $filterBuilder)
    {
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter(FilterInterface $filter): array
    {
        $filters = [];

        foreach ($this->clauseMapping as $clause => $method) {
            $subQueries = $this->getFilters($filter->$method());
            if (!empty($subQueries)) {
                $filters[$clause] = $subQueries;
            }
        }

        return $this->simplifyFilter($filters);
    }

    /**
     * Build all filters contained into an array.
     *
     * @param FilterInterface[] $filters
     *
     * @return array
     */
    private function getFilters(array $filters)
    {
        $filters = array_map([$this->filterBuilder, 'getFilter'], $filters);

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
