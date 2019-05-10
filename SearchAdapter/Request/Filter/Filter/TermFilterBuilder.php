<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Filter\Filter;

use Elastic\AppSearch\SearchAdapter\Request\Filter\FilterBuilderInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapterProvider;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapter;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldTypeResolverInterface;

/**
 * Implementation of the term filter builder.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class TermFilterBuilder implements FilterBuilderInterface
{
    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var FieldTypeResolverInterface
     */
    private $fieldTypeResolver;

    /**
     * @var AttributeAdapterProvider
     */
    private $attributeProvider;

    /**
     * Constructor.
     *
     * @param AttributeAdapterProvider   $attributeProvider
     * @param FieldNameResolverInterface $fieldNameResolver
     * @param FieldTypeResolverInterface $fieldTypeResolver
     */
    public function __construct(
        AttributeAdapterProvider $attributeProvider,
        FieldNameResolverInterface $fieldNameResolver,
        FieldTypeResolverInterface $fieldTypeResolver
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldTypeResolver = $fieldTypeResolver;
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
        $attribute = $this->getAttribute($requestFieldName);

        return $this->fieldNameResolver->getFieldName($attribute, ['type' => SchemaInterface::CONTEXT_FILTER]);
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
        $attribute = $this->getAttribute($requestFieldName);

        return $this->fieldTypeResolver->getFieldType($attribute);
    }

    /**
     * Request request attribute.
     *
     * @param string $requestFieldName
     *
     * @return AttributeAdapter
     */
    private function getAttribute(string $requestFieldName): AttributeAdapter
    {
        return $this->attributeProvider->getAttributeAdapter($requestFieldName);
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
