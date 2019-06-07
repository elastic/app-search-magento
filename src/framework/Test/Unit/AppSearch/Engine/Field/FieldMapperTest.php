<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\Field;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapper;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\AttributeAdapterProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\AttributeAdapterInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldTypeResolverInterface;


/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapper class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test getting field name through the field mapper.
     *
     * @testWith ["foo", {}, "foo"]
     *           ["foo", {"type": "search"}, "foosearch"]
     */
    public function testGetFieldName(string $attributeCode, array $context, string $fieldName)
    {
        $this->assertEquals($fieldName, $this->getFieldMapper()->getFieldName($attributeCode, $context));
    }

    /**
     * Test getting field type through the field mapper.
     */
    public function testGetFieldType($attributeCode = 'foo', $fieldType = 'text')
    {
        $this->assertEquals($fieldType, $this->getFieldMapper()->getFieldType($attributeCode));
    }

    /**
     * Field mapper used during tests.
     *
     * @param string $fieldName
     * @param string $fieldType
     *
     * @return FieldMapper
     */
    private function getFieldMapper(string $fieldName = 'foo', string $fieldType = 'text'): FieldMapper
    {
        $attributeAdapterProvider = $this->getAttributeAdapterProvider($fieldName, $fieldType);
        $fieldNameResolver        = $this->getFieldNameResolver();
        $fieldTypeResolver        = $this->getFieldTypeResolver();

        return new FieldMapper($attributeAdapterProvider, $fieldNameResolver, $fieldTypeResolver);
    }

    /**
     * Attribute adapter provider used during tests.
     *
     * @param string $fieldName
     * @param string $fieldType
     *
     * @return AttributeAdapterProviderInterface
     */
    private function getAttributeAdapterProvider(string $fieldName, string $fieldType): AttributeAdapterProviderInterface
    {
        $attributeAdapter = $this->createMock(AttributeAdapterInterface::class);

        $attributeAdapter->method('getAttributeCode')->willReturn($fieldName);
        $attributeAdapter->method('getFrontendInput')->willReturn($fieldType);

        $attributeAdapterProvider = $this->createMock(AttributeAdapterProviderInterface::class);
        $attributeAdapterProvider->method('getAttributeAdapter')->willReturn($attributeAdapter);

        return $attributeAdapterProvider;
    }

    /**
     * Field name resolver used during tests.
     *
     * @param string $fieldName
     *
     * @return FieldNameResolverInterface
     */
    private function getFieldNameResolver(): FieldNameResolverInterface
    {
        $getFieldNameStub = function (AttributeAdapterInterface $attributeAdapter, array $context): string {
            return $attributeAdapter->getAttributeCode() . ($context['type'] ?? '');
        };

        $fieldNameResolver = $this->createMock(FieldNameResolverInterface::class);
        $fieldNameResolver->method('getFieldName')->will($this->returnCallback($getFieldNameStub));

        return $fieldNameResolver;
    }

    /**
     * Field type resolver used during tests.
     *
     * @return FieldTypeResolverInterface
     */
    private function getFieldTypeResolver(): FieldTypeResolverInterface
    {
        $getFieldNameStub = function (AttributeAdapterInterface $attributeAdapter): string {
            return $attributeAdapter->getFrontendInput();
        };

        $fieldTypeResolver = $this->createMock(FieldTypeResolverInterface::class);
        $fieldTypeResolver->method('getFieldType')->will($this->returnCallback($getFieldNameStub));

        return $fieldTypeResolver;
    }
}
