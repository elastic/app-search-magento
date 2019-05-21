<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter;

use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Client\ConnectionManager;
use Elastic\AppSearch\SearchAdapter\Request\Fulltext\QueryTextResolverInterface;
use Elastic\AppSearch\Model\Adapter\EngineInterface;
use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Elastic\AppSearch\SearchAdapter\Request\RescorerResolverInterface;
use Elastic\AppSearch\SearchAdapter\Request\EngineResolver;

/**
 * Run the search request against the engine.
 *
 * @package   Elastic\AppSearch\SearchAdapter
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
     * @var RescorerResolverInterface
     */
    private $rescorerResolver;

    /**
     * Constructor.
     *
     * @param ConnectionManager             $connectionManager
     * @param EngineResolver                $engineResolver
     * @param SearchParamsProviderInterface $searchParamsProvider
     * @param QueryTextResolverInterface    $queryTextResolver
     * @param RescorerResolverInterface     $rescorerResolver
     */
    public function __construct(
        ConnectionManager $connectionManager,
        EngineResolver $engineResolver,
        SearchParamsProviderInterface $searchParamsProvider,
        QueryTextResolverInterface $queryTextResolver,
        RescorerResolverInterface $rescorerResolver
    ) {
        $this->client               = $connectionManager->getClient();
        $this->engineResolver       = $engineResolver;
        $this->searchParamsProvider = $searchParamsProvider;
        $this->queryTextResolver    = $queryTextResolver;
        $this->rescorerResolver     = $rescorerResolver;
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

        $rescorer = $this->rescorerResolver->getRescorer($request);

        $searchParams = $rescorer->prepareSearchParams($request, $searchParams);

        try {
            $response = $this->client->search($engine->getName(), $queryText, $searchParams);
        } catch (\Exception $e) {
            $response = ['results' => [], 'facets' => [], 'meta' => ['page' => ['total_results' => 0]]];
        }

        $response['facets']  = $this->parseFacets($request, $response);
        $response['results'] = $rescorer->rescoreResults($request, $response['results']);

        return $response;
    }

    /**
     * Parse result facets and convert into the format expected by the ResponseFactory.
     * Add missing facets to the result.
     *
     * @param RequestInterface $request
     * @param array            $response
     *
     * @return array
     */
    private function parseFacets(RequestInterface $request, array $response): array
    {
        $facets = [];

        foreach ($response['facets'] as $fieldFacets) {
            foreach ($fieldFacets as $facet) {
                $facets[$facet['name']] = $facet['data'];
            }
        }

        foreach ($request->getAggregation() as $bucket) {
            if (!isset($facets[$bucket->getName()])) {
                $facets[$bucket->getName()] = [];
            }
        }

        return $facets;
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
