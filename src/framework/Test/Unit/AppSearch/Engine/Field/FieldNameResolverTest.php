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

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolver;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\AttributeAdapterInterface;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolver class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldNameResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test name resolution for text attributes.
     *
     * @testWith ["foo", null, false, false, {"type": "search"}, "foo"]
     *            ["foo", null, false, false, {"type": "search"}, "foo"]
     *            ["foo", "select", false, false, {"type": "search"}, "foo"]
     *            ["foo", "select", true, false, {"type": "search"}, "foo_value"]
     *            ["foo", "select", false, true, {"type": "search"}, "foo_value"]
     *            ["foo", "select", false, true, {"type": "sort"}, "foo_value"]
     *            ["foo", "select", true, true, {"type": "filter"}, "foo"]
     *            ["foo", "multiselect", true, false, {"type": "search"}, "foo_value"]
     *            ["foo", "boolean", true, false, {"type": "search"}, "foo_value"]
     *            ["foo", "boolean", true, true, {"type": "sort"}, "foo"]
     */
    public function testGetFieldName($attributeCode, $frontendInput, $searchable, $sortable, $context, $expectedName)
    {
        $resolver         = new FieldNameResolver();

        $attributeAdapter = $this->createMock(AttributeAdapterInterface::class);
        $attributeAdapter->method('getAttributeCode')->willReturn($attributeCode);
        $attributeAdapter->method('getFrontendInput')->willReturn($frontendInput);
        $attributeAdapter->method('isSearchable')->willReturn($searchable);
        $attributeAdapter->method('isSortable')->willReturn($sortable);

        $this->assertEquals($expectedName, $resolver->getFieldName($attributeAdapter, $context));
    }
}
