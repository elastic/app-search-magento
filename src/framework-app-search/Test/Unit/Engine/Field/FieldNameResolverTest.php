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

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolver;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldInterface;

/**
 * Unit test for the FieldNameResolver class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\Engine\\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldNameResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test name resolution for various field config.
     *
     * @testWith ["foo", false, false, false, {"type": "search"}, "foo"]
     *            ["foo", false, false, false, {"type": "search"}, "foo"]
     *            ["foo", false, false, false, {"type": "search"}, "foo"]
     *            ["foo", true, true, false, {"type": "search"}, "foo_value"]
     *            ["foo", true, false, true, {"type": "search"}, "foo_value"]
     *            ["foo", true, false, true, {"type": "sort"}, "foo_value"]
     *            ["foo", true, true, true, {"type": "filter"}, "foo"]
     *            ["foo", true, true, false, {"type": "search"}, "foo_value"]
     *            ["foo", true, true, false, {"type": "search"}, "foo_value"]
     *            ["foo", false, true, true, {"type": "sort"}, "foo"]
     */
    public function testGetFieldName($fieldName, $useValue, $searchable, $sortable, $context, $expectedName)
    {
        $resolver = new FieldNameResolver();

        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn($fieldName);
        $field->method('useValueField')->willReturn($useValue);
        $field->method('isSearchable')->willReturn($searchable);
        $field->method('isSortable')->willReturn($sortable);

        $this->assertEquals($expectedName, $resolver->getFieldName($field, $context));
    }
}
