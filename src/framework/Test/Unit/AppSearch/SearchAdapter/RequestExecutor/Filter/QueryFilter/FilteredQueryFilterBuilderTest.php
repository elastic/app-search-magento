<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Filter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter\FilteredQueryFilterBuilder;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\FilterBuilderInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\FilterBuilderInterfaceFactory;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Query\Filter as FilteredQuery;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter\BoolQueryFilterBuilder class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class FilteredQueryFilterBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test building a valid filtered query.
     */
    public function testBuildValidQuery()
    {
        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getName')->willReturn('filterName');

        $query = $this->createMock(FilteredQuery::class);
        $query->method('getReference')->willReturn($filter);

        $this->assertEquals(['filterName'], $this->getQueryBuilder()->getFilter($query));
    }

    /**
     * Check an empty filter is returned when the query refers to an empty filter reference.
     */
    public function testBuildEmptyQuery()
    {
        $query = $this->createMock(FilteredQuery::class);

        $this->assertEquals([], $this->getQueryBuilder()->getFilter($query));
    }

    /**
     * Create the builder used in tests.
     *
     * @return FilteredQueryFilterBuilder
     */
    private function getQueryBuilder()
    {
        $filterBuilder = $this->createMock(FilterBuilderInterface::class);
        $filterBuilder->method('getFilter')->willReturnCallback(
            function ($filter) {
                return [$filter->getName()];
            }
        );

        $filterBuilderFactory = $this->createMock(FilterBuilderInterfaceFactory::class);
        $filterBuilderFactory->method('create')->willReturn($filterBuilder);

        $filterMapper = $this->createMock(FieldMapperInterface::class);

        return new FilteredQueryFilterBuilder($filterBuilderFactory, $filterMapper);
    }
}
