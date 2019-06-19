<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Filter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\Filter\RangeFilterBuilder;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Filter\Range as RangeFilter;

/**
 * Unit test for the RangeFilterBuilder class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Filter\QueryFilter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class RangeFilterBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Build range filters and check the results.
     *
     * @dataProvider filterDataProvider
     *
     * @param FilterInterface $filter
     */
    public function testGetFilter(FilterInterface $filter, array $expectedResult)
    {
        $fieldMapper = $this->createMock(FieldMapperInterface::class);
        $fieldMapper->expects($this->once())->method('getFieldName')->will($this->returnArgument(0));
        $fieldMapper->method('mapValue')->will($this->returnArgument(1));

        $builder = new RangeFilterBuilder($fieldMapper);

        $this->assertEquals($expectedResult, $builder->getFilter($filter));
    }

    /**
     * List of filter to get build and expected results.
     *
     * @return array
     */
    public function filterDataProvider()
    {
        return [
            [new RangeFilter('', 'foo', 0, 100), ['foo' => ['from' => 0, 'to' => 100]]],
            [new RangeFilter('', 'foo', '0', '100'), ['foo' => ['from' => '0', 'to' => '100']]],
            [new RangeFilter('', 'foo', 0, null), ['foo' => ['from' => 0]]],
            [new RangeFilter('', 'foo', null, 100), ['foo' => ['to' => 100]]],
            [new RangeFilter('', 'foo', null, null), []],
        ];
    }
}
