<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\ResponseBuilder;
use Elastic\AppSearch\Framework\Search\Response;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\DocumentInterface;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\SearchAdapter\ResponseBuilder class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class ResponseBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test building response.
     *
     * @testWith [{"results": [{"id": "doc1"}, {"id": "doc2"}], "count": 10}]
     */
    public function testBuildResponse(array $rawResponse = [])
    {
        $response = $this->getResponseBuilder()->buildResponse($rawResponse);

        $this->assertEquals($rawResponse['count'], $response->count());
        $this->assertInstanceOf(AggregationInterface::class, $response->getAggregations());

        $this->assertCount(count($rawResponse['results']), iterator_to_array($response));
        foreach ($response as $document) {
            $this->assertInstanceOf(DocumentInterface::class, $document);
        }
    }

    /**
     * Search response builder used during tests.
     *
     * @return ResponseBuilder
     */
    private function getResponseBuilder(): ResponseBuilder
    {
        $objectManager = new ObjectManager($this);
        $constructorArgs = $objectManager->getConstructArguments(ResponseBuilder::class);

        $constructorArgs['responseFactory']->expects($this->once())
          ->method('create')
          ->willReturnCallback($this->createResponseStub($objectManager));

        $constructorArgs['documentCountResolver']->expects($this->once())
          ->method('getDocumentCount')
          ->willReturnCallback($this->getDocumentCountStub());

        return $objectManager->getObject(ResponseBuilder::class, $constructorArgs);
    }

    /**
     * Response factory stub.
     *
     * @return callable
     */
    private function createResponseStub($objectManager): callable
    {
        return function ($data) use ($objectManager) {
            $data = $objectManager->getConstructArguments(Response::class, $data);

            return $objectManager->getObject(Response::class, $data);
        };
    }

    /**
     * Document count resolver stub.
     *
     * @return callable
     */
    private function getDocumentCountStub(): callable
    {
        return function ($response) {
            return $response['count'];
        };
    }
}
