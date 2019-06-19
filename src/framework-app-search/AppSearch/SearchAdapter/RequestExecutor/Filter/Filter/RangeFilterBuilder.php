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
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;

/**
 * Extract and build filters from the search request.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RangeFilterBuilder implements FilterBuilderInterface
{
    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * Constructor.
     *
     * @param FieldMapperInterface $fieldMapper
     */
    public function __construct(FieldMapperInterface $fieldMapper)
    {
        $this->fieldMapper = $fieldMapper;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter(FilterInterface $filter): array
    {
        $context    = ['type' => SchemaInterface::CONTEXT_FILTER];
        $filterName = $this->fieldMapper->getFieldName($filter->getField(), $context);

        $range = [];

        if ($filter->getFrom() !== null) {
            $range['from'] = $filter->getFrom();
        }

        if ($filter->getTo() !== null) {
            $range['to'] = $filter->getTo();
        }

        return !empty($range) ? [$filterName => $this->fieldMapper->mapValue($filter->getField(), $range)] : [];
    }
}
