<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilter;

use Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilterBuilderInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Elastic\AppSearch\SearchAdapter\Request\Filter\FilterBuilderInterface;

/**
 * Implementation of the filtered query filter builder.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FilteredQueryFilterBuilder implements QueryFilterBuilderInterface
{
    /**
     * @var FilterBuilderInterface
     */
    private $filterBuilder;

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
    public function getFilter(QueryInterface $query): array
    {
        $filter = [];

        if ($query->getReference() && $query->getReference() instanceof FilterInterface) {
            $filter = $this->filterBuilder->getFilter($query->getReference());
        }

        return $filter;
    }
}
