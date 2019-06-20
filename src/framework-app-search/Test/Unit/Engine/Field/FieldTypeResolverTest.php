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

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldTypeResolver;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldInterface;

/**
 * Unit test for the FieldTypeResolver class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\Engine\\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldTypeResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Default type resolution test.
     */
    public function testDefaultStringType()
    {
        $resolver = new FieldTypeResolver();

        $field = $this->createMock(FieldInterface::class);

        $this->assertEquals('text', $resolver->getFieldType($field));
    }

    /**
     * Test type resolution when set in the field.
     */
    public function testGetFieldType($type = 'number')
    {
        $resolver         = new FieldTypeResolver();

        $field = $this->createMock(FieldInterface::class);
        $field->method('getType')->willReturn($type);

        $this->assertEquals($type, $resolver->getFieldType($field));
    }
}
