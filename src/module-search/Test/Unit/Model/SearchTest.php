<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Search\Test\Unit\Model;

use Elastic\AppSearch\Search\Model\Search;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Elastic\AppSearch\Search\Model\Search\RequestBuilder;
use Magento\Framework\Search\SearchResponseBuilder;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Unit test for the Search class.
 *
 * @package   Elastic\AppSearch\Search\Test\Unit\Model
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test search using a search criteria.
     */
    public function testSearch()
    {
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);

        $search = new Search($this->getRequestBuilder(), $this->getSearchEngine(), $this->getResponseBuilder());

        $this->assertInstanceOf(SearchResultInterface::class, $search->search($searchCriteria));
    }

    /**
     * Mock the search engine used during test.
     *
     * @return SearchEngineInterface
     */
    private function getSearchEngine()
    {
        $response = $this->createMock(\Magento\Framework\Search\ResponseInterface::class);

        $searchEngine = $this->createMock(SearchEngineInterface::class);
        $searchEngine->expects($this->once())->method('search')->willReturn($response);

        return $searchEngine;
    }

    /**
     * Mock the request builder used during test.
     *
     * @return RequestBuilder
     */
    private function getRequestBuilder()
    {
        $request = $this->createMock(\Magento\Framework\Search\RequestInterface::class);

        $requestBuilder = $this->createMock(RequestBuilder::class);
        $requestBuilder->expects($this->once())->method('create')->willReturn($request);

        return $requestBuilder;
    }

    /**
     * Mock the search response builder used during test.
     *
     * @return SearchResponseBuilder
     */
    private function getResponseBuilder()
    {
        $searchResult = $this->createMock(SearchResultInterface::class);
        $searchResult->expects($this->once())->method('setSearchCriteria')->will($this->returnSelf());

        $searchResponseBuilder = $this->createMock(SearchResponseBuilder::class);
        $searchResponseBuilder->expects($this->once())->method('build')->willReturn($searchResult);

        return $searchResponseBuilder;
    }
}
