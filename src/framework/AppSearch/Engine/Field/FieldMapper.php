<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field;

/**
 * Default field mapper implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldMapper implements FieldMapperInterface
{
    /**
     * @var AttributeAdapterProviderInterface
     */
    private $attributeAdapterProvider;

    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var FieldTypeResolverInterface
     */
    private $fieldTypeResolver;

    /**
     * @var FieldValueMapperInterface[]
     */
    private $fieldValueMappers;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param AttributeAdapterProviderInterface $attributeAdapterProvider
     * @param FieldNameResolverInterface        $fieldNameResolver
     * @param FieldTypeResolverInterface        $fieldTypeResolver
     * @param FieldValueMapperInterface[]       $fieldValueMappers
     */
    public function __construct(
        AttributeAdapterProviderInterface $attributeAdapterProvider,
        FieldNameResolverInterface $fieldNameResolver,
        FieldTypeResolverInterface $fieldTypeResolver,
        array $fieldValueMappers = []
    ) {
        $this->attributeAdapterProvider = $attributeAdapterProvider;
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldTypeResolver = $fieldTypeResolver;
        $this->fieldValueMappers = $fieldValueMappers;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldName(string $attributeCode, array $context = []): string
    {
        return $this->fieldNameResolver->getFieldName($this->getAttribute($attributeCode), $context);
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldType(string $attributeCode): string
    {
        return $this->fieldTypeResolver->getFieldType($this->getAttribute($attributeCode));
    }

    /**
     * {@inheritDoc}
     */
    public function mapValue($attributeCode, $value)
    {
        $fieldType = $this->getFieldType($attributeCode);

        if (isset($this->fieldValueMappers[$fieldType])) {
            $value = $this->fieldValueMappers[$fieldType]->mapValue($value);
        }

        return $value;
    }

    /**
     * Find the attribute using the attribute provider.
     *
     * @param string $attributeCode
     *
     * @return AttributeAdapterInterface
     */
    private function getAttribute(string $attributeCode): AttributeAdapterInterface
    {
        return $this->attributeAdapterProvider->getAttributeAdapter($attributeCode);
    }
}
