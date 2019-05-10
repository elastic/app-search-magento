<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Search\Request\Builder;

use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Filter\BoolExpressionFactory as BoolFilterFactory;
use Magento\Framework\Search\Request\Filter\TermFactory as TermFilterFactory;
use Magento\Framework\Search\Request\Filter\RangeFactory as RangeFilterFactory;
use Magento\Framework\Api\Filter;

/**
 * Build a query to represent filter.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\Search\Request\Builder
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FilterBuilder
{
    /**
     * @var float
     */
    const RANGE_PRECISION = 0.000000001;

    /**
     * @var array
     */
    private $conditionsMapping = [
        'from'   => 'from',
        'gt'     => 'from',
        'gteq'   => 'from',
        'moreq'  => 'from',
        'to'     => 'to',
        'lt'     => 'to',
        'lteq'   => 'to',
    ];

    /**
     * @var array
     */
    private $filterFactories = [];

    /**
     * Constructor.
     *
     * @param FilteredQueryFactory $filteredQueryFactory
     * @param BoolFilterFactory    $boolFilterFactory
     * @param TermFilterFactory    $termFilterFactory
     * @param RangeFilterFactory   $rangeFilterFactory
     */
    public function __construct(
        BoolFilterFactory $boolFilterFactory,
        TermFilterFactory $termFilterFactory,
        RangeFilterFactory $rangeFilterFactory
    ) {
        $this->filterFactories['bool']  = $boolFilterFactory;
        $this->filterFactories['term']  = $termFilterFactory;
        $this->filterFactories['range'] = $rangeFilterFactory;
    }

    /**
     * Generate the query to match a filter.
     *
     * @param Filter $filter
     *
     * @return FilterInterface
     */
    public function create(Filter $filter): FilterInterface
    {
        $conditionType = $this->conditionsMapping[$filter->getConditionType()] ?? $filter->getConditionType();

        if (in_array($conditionType, ['from', 'to'])) {
            return $this->prepareRangeFilter($filter);
        }

        return $this->prepareTermFilter($filter);
    }

    /**
     * Generate a range filter.
     *
     * @param Filter $filter
     *
     * @return FilterInterface
     */
    private function prepareRangeFilter(Filter $filter): FilterInterface
    {
        $conditionValue = $filter->getValue();
        $conditionType  = $this->conditionsMapping[$filter->getConditionType()] ?? $filter->getConditionType();

        if ($filter->getConditionType() === 'gt') {
            $conditionValue = $conditionValue + self::RANGE_PRECISION;
        } elseif ($filter->getConditionType() === 'lteq') {
            $conditionValue = $conditionValue + self::RANGE_PRECISION;
        }

        $rangeParams = ['name' => '', 'field' => $filter->getField(), $conditionType => $conditionValue];

        if (!isset($rangeParams['from'])) {
            $rangeParams['from'] = null;
        }

        if (!isset($rangeParams['to'])) {
            $rangeParams['to'] = null;
        }

        return $this->filterFactories['range']->create($rangeParams);
    }

    /**
     * Generate a term filter.
     *
     * @param Filter $filter
     *
     * @return FilterInterface
     */
    private function prepareTermFilter(Filter $filter): FilterInterface
    {
        $filterParams = ['name' => '', 'value' => $filter->getValue(), 'field' => $filter->getField()];
        $termFilter = $this->filterFactories['term']->create($filterParams);

        if (in_array($filter->getConditionType(), ['neq', 'nin'])) {
            $termFilter = $this->filterFactories['bool']->create(['name' => '', 'not' => [$termFilter]]);
        }

        return $termFilter;
    }
}
