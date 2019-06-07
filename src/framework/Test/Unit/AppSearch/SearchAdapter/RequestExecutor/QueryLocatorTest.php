<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor;

use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\QueryLocator;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\QueryLocator class.
 *
 * @package   namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class QueryLocatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test fulltext query extraction accross various search requests.
     *
     * @dataProvider sampleRequestsDataProvider
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * @param RequestInterface $request
     */
    public function testGetQuery(RequestInterface $request, $hasText)
    {
        $queryLocator = new QueryLocator();

        $query = $queryLocator->getQuery($request);

        if (!$hasText) {
            $this->assertNull($query);
        } else {
            $this->assertInstanceOf(QueryInterface::class, $query);
            $this->assertEquals("search", $query->getName());
        }
    }

    /**
     * Provides search requests used for the tests.
     *
     * @return RequestInterface[]
     */
    public function sampleRequestsDataProvider()
    {
        $requests = [];

        foreach ($this->getSampleQueries() as $query) {
            list($query, $hasText) = $query;
            $request = $this->createMock(RequestInterface::class);
            $request->method('getQuery')->willReturn($query);
            $requests[] = [$request, $hasText];
        }

        return $requests;
    }

    /**
     * Return queries used for the tests and a boolean flg indicating if the query is a fulltext query.
     *
     * @return array
     */
    private function getSampleQueries()
    {
        $searchQuery = $this->createMock(QueryInterface::class);
        $searchQuery->method('getName')->willReturn("search");
        $searchQuery->method('getType')->willReturn(QueryInterface::TYPE_MATCH);

        $otherQuery = $this->createMock(QueryInterface::class);

        return [
            [null, false],
            [$searchQuery, true],
            [$otherQuery, false],
            [$this->createBoolQuery([], []), false],
            [$this->createBoolQuery([$searchQuery], []), true],
            [$this->createBoolQuery([], [$searchQuery]), true],
            [$this->createBoolQuery([$otherQuery], [$searchQuery]), true],
            [$this->createBoolQuery([$searchQuery], [$otherQuery]), true],
            [$this->createBoolQuery([$otherQuery], []), false],
            [$this->createBoolQuery([], [$otherQuery]), false],
            [$this->createBoolQuery([$searchQuery, $otherQuery], []), true],
            [$this->createBoolQuery([$this->createBoolQuery([$searchQuery])], [$otherQuery]), true],
            [$this->createBoolQuery([$this->createBoolQuery([$searchQuery]), $otherQuery]), true],
        ];
    }

    /**
     * Generate a boolean query.
     *
     * @param array $must
     * @param array $should
     *
     * @return QueryInterface
     */
    private function createBoolQuery($must = [], $should = [])
    {
        $methods = ['getType', 'getMust', 'getShould', 'getName', 'getBoost'];
        $query   = $this->createPartialMock(QueryInterface::class, $methods);

        $query->method('getType')->willReturn(QueryInterface::TYPE_BOOL);
        $query->method('getMust')->willReturn($must);
        $query->method('getShould')->willReturn($should);

        return $query;
    }
}
