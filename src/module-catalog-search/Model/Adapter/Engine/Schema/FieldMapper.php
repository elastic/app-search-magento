<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema;

/**
 * Field mapper implementation.
 *
 * @package   Elastic\Model\Adapter\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldMapper implements FieldMapperInterface
{
    /**
     * @var AttributeAdapterProvider
     */
    private $attributeProvider;

    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var FieldTypeResolverInterface
     */
    private $fieldTypeResolver;

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
        return $value;
    }

    /**
     * Find the attribute using the attribute provider.
     *
     * @param string $attributeCode
     *
     * @return AttributeAdapter
     */
    private function getAttribute(string $attributeCode): AttributeAdapter
    {
        return $this->attributeProvider->getAttributeAdapter($attributeCode);
    }
}
