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
use Elastic\AppSearch\Model\Adapter\EngineResolverInterface;
use Elastic\AppSearch\SearchAdapter\Request\Fulltext\QueryTextResolverInterface;
use Elastic\AppSearch\Model\Adapter\EngineInterface;
use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

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
     * @var EngineResolverInterface
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
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor.
     *
     * @param ConnectionManager             $connectionManager
     * @param EngineResolverInterface       $engineResolver
     * @param SearchParamsProviderInterface $searchParamsProvider
     * @param QueryTextResolverInterface    $queryTextResolver
     * @param ScopeResolverInterface        $scopeResolver
     * @param StoreManagerInterface         $storeManager
     */
    public function __construct(
        ConnectionManager $connectionManager,
        EngineResolverInterface $engineResolver,
        SearchParamsProviderInterface $searchParamsProvider,
        QueryTextResolverInterface $queryTextResolver,
        ScopeResolverInterface $scopeResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->client               = $connectionManager->getClient();
        $this->engineResolver       = $engineResolver;
        $this->searchParamsProvider = $searchParamsProvider;
        $this->queryTextResolver    = $queryTextResolver;
        $this->scopeResolver        = $scopeResolver;
        $this->storeManager         = $storeManager;
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
            $response = ['results' => [], 'facets' => [], 'meta' => ['page' => ['total_results' => 0]]];
        }

        $response['facets'] = $this->parseFacets($request, $response);
        $response['results'] = $this->recomputeScores($response);

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
     * Fix doc score to ensure that positionned docs have the higher scores.
     *
     * @param array $response
     *
     * @return array
     */
    private function recomputeScores($response): array
    {
        $results   = $response['results'];

        $docScores = array_map([$this, 'getDocScore'], $results);
        if (!empty($docScores)) {
            $maxScore  = max(...$docScores);

            if ($maxScore > 0 && $this->getDocScore(current($results)) < $maxScore) {
                $currentScore = count($results) + max(...$docScores);
                $docIndex     = 0;

                while (isset($results[$docIndex]) && $this->getDocScore($results[$docIndex]) < $maxScore) {
                    $results[$docIndex]['_meta']['score'] = $currentScore;
                    $currentScore--;
                    $docIndex++;
                }
            }
        }

        return $results;
    }

    /**
     * Return score for a doc.
     *
     * @param array $doc
     *
     * @return int
     */
    private function getDocScore($doc): int
    {
        return (int) $doc['_meta']['score'];
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
        return $this->engineResolver->getEngine($request->getIndex(), $this->getStoreId($request));
    }

    /**
     * Resolve store id from the search request.
     *
     * @param RequestInterface $request
     *
     * @return int
     */
    private function getStoreId(RequestInterface $request): int
    {
        $dimension = current($request->getDimensions());
        $storeId   = $this->scopeResolver->getScope($dimension->getValue())->getId();

        if ($storeId == 0) {
            $storeId = $this->storeManager->getDefaultStoreView()->getId();
        }

        return $storeId;
    }
}
