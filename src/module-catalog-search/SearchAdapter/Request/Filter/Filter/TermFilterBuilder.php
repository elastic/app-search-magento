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
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\FieldMapperInterface;

/**
 * Implementation of the term filter builder.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class TermFilterBuilder implements FilterBuilderInterface
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
        $fieldType = $this->getFieldType($filter->getField());

        return [$fieldName => $this->prepareFilterValue($filter->getValue(), $fieldType)];
    }

    /**
     * Convert the field name to match the indexed data.
     *
     * @param string $requestFieldName
     *
     * @return string
     */
    private function getFieldName(string $requestFieldName): string
    {
        return $this->fieldMapper->getFieldName($requestFieldName, ['type' => SchemaInterface::CONTEXT_FILTER]);
    }

    /**
     * Return request expected field type.
     *
     * @param string $requestFieldName
     *
     * @return string
     */
    private function getFieldType(string $requestFieldName): string
    {
        return $this->fieldMapper->getFieldType($requestFieldName);
    }

    /**
     * Coerce filter value to the expected type.
     *
     * @param mixed  $rawValue
     * @param string $fieldType
     *
     * @return mixed
     */
    private function prepareFilterValue($rawValue, string $fieldType)
    {
        if (is_array($rawValue)) {
            $callback = function ($value) use ($fieldType) {
                return $this->prepareFilterValue($value, $fieldType);
            };
            return array_map($callback, $rawValue);
        }

        $value = $rawValue;

        if ($fieldType == SchemaInterface::FIELD_TYPE_TEXT) {
            $value = strval($rawValue);
        } elseif ($fieldType == SchemaInterface::FIELD_TYPE_NUMBER) {
            $value = floatval($rawValue);
        }

        return $value;
    }
}
