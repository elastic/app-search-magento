<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Test\Unit\SearchAdapter;

use Elastic\AppSearch\SearchAdapter\RequestExecutor;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Client\ConnectionManager;
use Swiftype\AppSearch\Client as AppSearchClient;
use Elastic\AppSearch\SearchAdapter\Request\EngineResolver;
use Elastic\AppSearch\Model\Adapter\EngineInterface;
use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Elastic\AppSearch\SearchAdapter\Request\Fulltext\QueryTextResolverInterface;
use Elastic\AppSearch\SearchAdapter\Request\RescorerResolverInterface;
use Elastic\AppSearch\SearchAdapter\RequestExecutor\Response\ProcessorInterface;

/**
 * Unit test for the Elastic\AppSearch\SearchAdapter\RequestExecutor class.
 *
 * @package   Elastic\AppSearch\Test\Unit\SearchAdapter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class RequestExecutorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    const ENGINE_NAME = 'enginename';

    /**
     * Test running search request using search executor.
     *
     * @testWith ["", {}]
     *           ["search text", {}]
     *           ["", {"foo": "bar"}]
     *           ["search text", {"foo": "bar"}]
     */
    public function testSearchParams($queryText, $searchParams)
    {
        $request = $this->createMock(RequestInterface::class);

        $searchMethod = function ($engineName, $queryText, $searchRequestParams) {
            return ['engine' => $engineName, 'query' => $queryText, 'params' => $searchRequestParams];
        };

        $client = $this->createMock(AppSearchClient::class);
        $client->expects($this->once())->method('search')->will($this->returnCallback($searchMethod));

        $response = $this->getRequestExecutor($client, $queryText, $searchParams)->execute($request);

        $this->assertEquals(self::ENGINE_NAME, $response['engine']);
        $this->assertEquals($queryText, $response['query']);
        $this->assertEquals($searchParams, $response['params']);
    }

    /**
     * Test client exception handling.
     */
    public function testExceptionHandling()
    {
        $request = $this->createMock(RequestInterface::class);
        $client = $this->createMock(AppSearchClient::class);
        $client->method('search')->will($this->throwException(new \Exception('message')));

        $response = $this->getRequestExecutor($client)->execute($request);

        $this->assertEmpty($response['results']);
        $this->assertEquals(0, $response['meta']['page']['total_results']);
    }

    /**
     * Instantiate a request excutor to be used in tests.
     *
     * @param AppSearchClient $client
     * @param string          $queryText
     * @param array           $searchParams
     *
     * @return RequestExecutor
     */
    private function getRequestExecutor(
        AppSearchClient $client = null,
        string $queryText = '',
        array $searchParams = []
    ) {
        $connectionManager    = $this->getConnectionManager($client);
        $engineResolver       = $this->getEngineResolver();
        $searchParamsProvider = $this->getSearchParamsProvider($searchParams);
        $queryTextResolver    = $this->getQueryTextResolver($queryText);
        $responseProcessor    = $this->getResponseProcessor();

        return new RequestExecutor(
            $connectionManager,
            $engineResolver,
            $searchParamsProvider,
            $queryTextResolver,
            $responseProcessor
        );
    }

    /**
     * Create connection manager used during tests.
     *
     * @return ConnectionManager
     */
    private function getConnectionManager(AppSearchClient $client = null)
    {
        if ($client === null) {
            $client = $this->createMock(AppSearchClient::class);
        }

        $connectionManager = $this->createMock(ConnectionManager::class);
        $connectionManager->expects($this->once())->method('getClient')->willReturn($client);

        return $connectionManager;
    }

    /**
     * Create engine resolver used during tests.
     *
     * @return EngineResolver
     */
    private function getEngineResolver()
    {
        $engine = $this->createMock(EngineInterface::class);
        $engine->expects($this->once())->method('getName')->willReturn(self::ENGINE_NAME);

        $engineResolver = $this->createMock(EngineResolver::class);
        $engineResolver->expects($this->once())->method('getEngine')->willReturn($engine);

        return $engineResolver;
    }

    /**
     * Create search params provider used during tests.
     *
     * @param array $params
     *
     * @return SearchParamsProviderInterface
     */
    private function getSearchParamsProvider(array $params = [])
    {
        $searchParamsProvider = $this->createMock(SearchParamsProviderInterface::class);
        $searchParamsProvider->expects($this->once())->method('getParams')->willReturn($params);

        return $searchParamsProvider;
    }

    /**
     * Return query text resolver used during tests.
     *
     * @param string $queryText
     *
     * @return QueryTextResolverInterface
     */
    private function getQueryTextResolver(string $queryText = '')
    {
        $resolver = $this->createMock(QueryTextResolverInterface::class);
        $resolver->expects($this->once())->method('getText')->willReturn($queryText);

        return $resolver;
    }

    /**
     * Return rescorer used during tests.
     *
     * @return RescorerResolverInterface
     */
    private function getResponseProcessor()
    {
        $responseProcessor = $this->createMock(ProcessorInterface::class);
        $responseProcessor->expects($this->once())->method('process')->will($this->returnArgument(1));

        return $responseProcessor;
    }
}
