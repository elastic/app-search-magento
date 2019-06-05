<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\Filter;

use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\FilterBuilderInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\SchemaInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\FieldMapperInterface;

/**
 * Extract and build filters from the search request.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\Filter
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
        $fieldName = $this->getFieldName($filter->getField());

        $range = array_map('floatval', array_filter(
            ['from' => $filter->getFrom(), 'to' => $filter->getTo()]
        ));

        return !empty($range) ? [$fieldName => array_filter($range)] : [];
    }

    /**
     * Convert the field name to match the indexed data.
     *
     * @param string $requestFieldName
     *
     * @return string
     */
    private function getFieldName(string $requestFieldName)
    {
        return $this->fieldMapper->getFieldName($requestFieldName, ['type' => SchemaInterface::CONTEXT_FILTER]);
    }
}
