<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter;

use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\Client\ConnectionManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Fulltext\QueryTextResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\SearchParamsProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\EngineResolver;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\ResponseProcessorInterface;

/**
 * Run the search request against the engine.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RequestExecutor
{
    /**
     * @var \Swiftype\AppSearch\Client
     */
    private $client;

    /**
     * @var EngineResolver
     */
    private $engineResolver;

    /**
     * @var SearchParamsProviderInterface
     */
    private $searchParamsProvider;

    /**
     * @var QueryTextResolverInterface
     */
    private $queryTextResolver;

    /**
     * @var ResponseProcessorInterface
     */
    private $responseProcessor;

    /**
     * Constructor.
     *
     * @param ConnectionManagerInterface    $connectionManager
     * @param EngineResolver                $engineResolver
     * @param SearchParamsProviderInterface $searchParamsProvider
     * @param QueryTextResolverInterface    $queryTextResolver
     * @param ResponseProcessorInterface    $responseProcessor
     */
    public function __construct(
        ConnectionManagerInterface $connectionManager,
        EngineResolver $engineResolver,
        SearchParamsProviderInterface $searchParamsProvider,
        QueryTextResolverInterface $queryTextResolver,
        ResponseProcessorInterface $responseProcessor
    ) {
        $this->client               = $connectionManager->getClient();
        $this->engineResolver       = $engineResolver;
        $this->searchParamsProvider = $searchParamsProvider;
        $this->queryTextResolver    = $queryTextResolver;
        $this->responseProcessor    = $responseProcessor;
    }

    /**
     * Run the search request.
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    public function execute(RequestInterface $request)
    {
        $searchParams = $this->searchParamsProvider->getParams($request);
        $engine       = $this->getEngine($request);
        $queryText    = $this->queryTextResolver->getText($request);

        try {
            $response = $this->client->search($engine->getName(), $queryText, $searchParams);
        } catch (\Exception $e) {
            $response = ['results' => [], 'meta' => ['page' => ['total_results' => 0]]];
        }

        return $this->responseProcessor->process($request, $response);
    }

    /**
     * Resolve the engine of the current request.
     *
     * @param RequestInterface $request
     *
     * @return EngineInterface
     */
    private function getEngine(RequestInterface $request): EngineInterface
    {
        return $this->engineResolver->getEngine($request);
    }
}
