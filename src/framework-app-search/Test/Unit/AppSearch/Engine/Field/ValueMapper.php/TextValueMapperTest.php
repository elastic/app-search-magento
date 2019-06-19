<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\Field\ValueMapper;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\ValueMapper\TextValueMapper;

/**
 * Unit test for the TextValueMapper class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\Field\ValueMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class TextValueMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test field mapper resolution.
     *
     * @testWith ["foo", "foo"]
     *           [1, "1"]
     *           [1.13, "1.13"]
     *           [[], []]
     *           [["foo", "bar"], ["foo", "bar"]]
     *           [["foo", 1], ["foo", "1"]]
     */
    public function testMapValue($sourceValue, $expectedResult)
    {
        $valueMapper = new TextValueMapper();

        $this->assertEquals($expectedResult, $valueMapper->mapValue($sourceValue));
    }
}
