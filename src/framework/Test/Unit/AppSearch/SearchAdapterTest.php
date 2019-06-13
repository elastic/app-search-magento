<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\ResponseInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\ResponseBuilder;

/**
 * Unit test for the SearchAdapter class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchAdapterTest extends \PHPUnit\Framework\TestCase
{
    public function testSearch()
    {
        $request = $this->createMock(RequestInterface::class);

        $requestExecutor = $this->createMock(RequestExecutor::class);
        $requestExecutor->expects($this->once())->method('execute')->willReturn([]);

        $responseBuilder = $this->createMock(ResponseBuilder::class);
        $responseBuilder->expects($this->once())->method('buildResponse');

        $searchAdapter = new SearchAdapter($requestExecutor, $responseBuilder);

        $searchResponse = $searchAdapter->query($request);

        $this->assertInstanceOf(ResponseInterface::class, $searchResponse);
    }
}
