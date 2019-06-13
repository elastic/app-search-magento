<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilterBuilderInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\FilterBuilderInterfaceFactory;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\FilterBuilderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;

/**
 * Implementation of the filtered query filter builder.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter
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
     * @param FilterBuilderInterfaceFactory $filterBuilderFactory
     * @param FieldMapperInterface          $fieldMapper
     */
    public function __construct(FilterBuilderInterfaceFactory $filterBuilderFactory, FieldMapperInterface $fieldMapper)
    {
        $this->filterBuilder = $filterBuilderFactory->create(['fieldMapper' => $fieldMapper]);
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
