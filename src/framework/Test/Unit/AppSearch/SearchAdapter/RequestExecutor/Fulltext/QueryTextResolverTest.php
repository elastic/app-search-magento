<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Fulltext;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Fulltext\QueryTextResolver;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\QueryLocatorInterface;

/**
 * Unit test for the QueryTextResolver class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Fulltext
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class QueryTextResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test exctracting search text accross various search request.
     *
     * @dataProvider sampleQueriesDataProvider
     */
    public function testGetText($query, $expectedText = "")
    {
        $request      = $this->getRequestMock($query);
        $queryLocator = $this->getQueryLocatorMock();
        $textResolver = new QueryTextResolver($queryLocator);

        $text = $textResolver->getText($request);

        $this->assertInternalType("string", $text);
        $this->assertEquals($expectedText, $text);
    }

    /**
     * List of query used in the test and expected extracted text.
     *
     * @return array
     */
    public function sampleQueriesDataProvider()
    {
        $queries = [[null]];
        $methods = $methods = ['getType', 'getName', 'getBoost', 'getValue'];

        $queryMock = $this->createPartialMock(QueryInterface::class, $methods);
        $queryMock->method('getValue')->willReturn("search text");
        $queries[] = [$queryMock, "search text"];

        $queryMock = $this->createPartialMock(QueryInterface::class, $methods);
        $queryMock->method('getValue')->willReturn(null);
        $queries[] = [$queryMock];

        return $queries;
    }

    /**
     * Mock a search request with a simple query.
     *
     * @param QueryInterface $query
     *
     * @return RequestInterface
     */
    private function getRequestMock(?QueryInterface $query)
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getQuery')->willReturn($query);

        return $request;
    }

    /**
     * Mock the query locator used to extract the match query that contain the searched text.
     *
     * @return QueryLocatorInterface
     */
    private function getQueryLocatorMock()
    {
        $queryLocator = $this->createMock(QueryLocatorInterface::class);
        $queryLocator->method('getQuery')->willReturnCallback(function ($request) {
            return $request->getQuery();
        });

        return $queryLocator;
    }
}
