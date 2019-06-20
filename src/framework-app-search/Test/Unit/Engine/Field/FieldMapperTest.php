<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\Engine\Field;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapper;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldTypeResolverInterface;

/**
 * Unit test for the FieldMapper class.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\Engine\\Field
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
    public function testGetFieldName(string $field, array $context, string $fieldName)
    {
        $this->assertEquals($fieldName, $this->getFieldMapper()->getFieldName($field, $context));
    }

    /**
     * Test getting field type through the field mapper.
     */
    public function testGetFieldType($field = 'foo', $fieldType = 'text')
    {
        $this->assertEquals($fieldType, $this->getFieldMapper()->getFieldType($field));
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
        $fieldProvider     = $this->getFieldProvider($fieldName, $fieldType);
        $fieldNameResolver = $this->getFieldNameResolver();
        $fieldTypeResolver = $this->getFieldTypeResolver();

        return new FieldMapper($fieldProvider, $fieldNameResolver, $fieldTypeResolver);
    }

    /**
     * Field provider used during tests.
     *
     * @param string $fieldName
     * @param string $fieldType
     *
     * @return FieldProviderInterface
     */
    private function getFieldProvider(string $fieldName, string $fieldType): FieldProviderInterface
    {
        $field = $this->createMock(FieldInterface::class);

        $field->method('getName')->willReturn($fieldName);
        $field->method('getType')->willReturn($fieldType);

        $fieldProvider = $this->createMock(FieldProviderInterface::class);
        $fieldProvider->method('getField')->willReturn($field);

        return $fieldProvider;
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
        $getFieldNameStub = function (FieldInterface $field, array $context): string {
            return $field->getName() . ($context['type'] ?? '');
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
        $getFieldNameStub = function (FieldInterface $field): string {
            return $field->getType();
        };

        $fieldTypeResolver = $this->createMock(FieldTypeResolverInterface::class);
        $fieldTypeResolver->method('getFieldType')->will($this->returnCallback($getFieldNameStub));

        return $fieldTypeResolver;
    }
}
