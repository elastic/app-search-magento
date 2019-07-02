<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Filter\Filter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\Filter\TermFilterBuilder;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Filter\Term as TermFilter;

/**
 * Unit test for the RangeFilterBuilder class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Filter\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class TermFilterBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Build term filters and check the results.
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

        $builder = new TermFilterBuilder($fieldMapper);

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
            [new TermFilter('', 'bar', 'foo'), ['foo' => 'bar']],
            [new TermFilter('', 1, 'foo'), ['foo' => 1]],
            [new TermFilter('', ['bar'], 'foo'), ['foo' => ['bar']]],
            [new TermFilter('', ['bar', 'baz'], 'foo'), ['foo' => ['bar', 'baz']]],
            [new TermFilter('', [], 'foo'), ['foo' => []]],
        ];
    }
}
