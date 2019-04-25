<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Test\Unit\SearchAdapter\Request\Filter\QueryFilter;

use Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilter\BoolQueryFilterBuilder;
use Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilterBuilderInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\Request\Query\BoolExpression;

/**
 * Unit test for the Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilter\BoolQueryFilterBuilder class.
 *
 * @package   Elastic\AppSearch\Test\Unit\SearchAdapter\Request\Filter\QueryFilter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class BoolQueryFilterBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Run the boolean query builder accross various sample and check the result.
     *
     * @dataProvider sampleQueries
     *
     * @param QueryInterface $query
     * @param array          $expectedResult
     */
    public function testBuildFilter($query, $expectedResult)
    {
        $filter = $this->getQueryBuilder()->getFilter($query);

        $this->assertEquals($expectedResult, $filter);
    }

    /**
     * Sample queries and results used for the test.
     *
     * @return array
     */
    public function sampleQueries()
    {
        // Query used inside bool clauses.
        $query1 = $this->createQuery("query1");
        $query2 = $this->createQuery("query2");
        $query3 = $this->createQuery("query3");

        // An empty query that should not be present into the output.
        $emptyQuery = $this->createQuery("emptyQuery1", "empty");

        return [
            [
              $this->createBoolQuery(),
              [],
            ],
            [
              $this->createBoolQuery([$query1, $query2]),
              ["all" => [["query1"], ["query2"]]],
            ],
            [
              $this->createBoolQuery([], [$query1, $query2]),
              ["any" => [["query1"], ["query2"]]],
            ],
            [
              $this->createBoolQuery([], [], [$query1, $query2]),
              ["none" => [["query1"], ["query2"]]],
            ],
            [
              $this->createBoolQuery([$query1, $emptyQuery]),
              [["query1"]],
            ],
            [
              $this->createBoolQuery([$query1], [$query2]),
              ["all" => [["query1"], ["query2"]]]
            ],
            [
              $this->createBoolQuery([$query1], [$query2, $query3], [$query3]),
              ["all" => [["query1"]], "any" => [["query2"], ["query3"]], "none" => [["query3"]]]
            ],
        ];
    }

    /**
     * Create the builder used in tests.
     *
     * @return BoolQueryFilterBuilder
     */
    private function getQueryBuilder()
    {
        $queryBuilder = $this->createMock(QueryFilterBuilderInterface::class);
        $queryBuilder->method('getFilter')->willReturnCallback(
            function ($query) {
                return $query->getType() != "empty" ? [$query->getName()] : [];
            }
        );

        return new BoolQueryFilterBuilder($queryBuilder);
    }

    /**
     * Mock query that can be used in tests.
     *
     * @param string $name
     * @param string $type
     *
     * @return QueryInterface
     */
    private function createQuery(string $name, string $type = "query")
    {
        $query = $this->createMock(QueryInterface::class);

        $query->method('getName')->willReturn($name);
        $query->method('getType')->willReturn($type);

        return $query;
    }

    /**
     * Mock a boolean query usable in tests.
     *
     * @param array $must
     * @param array $should
     * @param array $mustNot
     *
     * @return QueryInterface
     */
    private function createBoolQuery($must = [], $should = [], $mustNot = [])
    {
        $query = $this->createMock(BoolExpression::class);

        $query->method('getMust')->willReturn($must);
        $query->method('getShould')->willReturn($should);
        $query->method('getMustNot')->willReturn($mustNot);

        return $query;
    }
}
