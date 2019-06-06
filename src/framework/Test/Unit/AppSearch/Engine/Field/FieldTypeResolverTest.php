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

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldTypeResolver;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\AttributeAdapterInterface;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldTypeResolver class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldTypeResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test type resolution for text attributes.
     */
    public function testStringType()
    {
        $resolver         = new FieldTypeResolver();
        $attributeAdapter = $this->createMock(AttributeAdapterInterface::class);

        $this->assertEquals('text', $resolver->getFieldType($attributeAdapter));
    }

    /**
     * Test type resolution for numeric attributes.
     */
    public function testNumericType()
    {
        $resolver         = new FieldTypeResolver();
        $attributeAdapter = $this->createMock(AttributeAdapterInterface::class);
        $attributeAdapter->method('isNumberType')->willReturn(true);

        $this->assertEquals('number', $resolver->getFieldType($attributeAdapter));
    }

    /**
     * Test type resolution for date attributes.
     */
    public function testDateType()
    {
        $resolver         = new FieldTypeResolver();
        $attributeAdapter = $this->createMock(AttributeAdapterInterface::class);
        $attributeAdapter->method('isDateType')->willReturn(true);

        $this->assertEquals('date', $resolver->getFieldType($attributeAdapter));
    }
}
