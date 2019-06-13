<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Rescorer;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\RescorerInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\Client\ConnectionManagerInterface;
use Swiftype\AppSearch\Client;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\EngineResolver;
// phpcs:disable
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Analytics\SearchParamsProvider as AnalytcisParams;
// phpcs:enable
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Page\SearchParamsProvider as PageParams;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Fulltext\QueryTextResolverInterface;

/**
 * Ensure score is consistent with the document positions.
 * Mostly intended to fix curated search results scores.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Rescorer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DefaultRescorer implements RescorerInterface
{
    /**
     * @var string
     */
    private const ANALYTICS_TAG = 'fulltext_rescorer';

    /**
     * @var float
     */
    private const RESCORE_INCREMENT = 0.01;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var EngineResolver
     */
    private $engineResolver;

    /**
     * @var ResultSorter
     */
    private $resultSorter;

    /**
     * @var QueryTextResolverInterface
     */
    private $queryTextResolver;

    /**
     * Constructor.
     *
     * @param QueryTextResolverInterface $queryTextResolver,
     * @param ResultSorter               $resultSorter
     * @param ConnectionManagerInterface $connectionManager
     * @param EngineResolver             $engineResolver
     */
    public function __construct(
        QueryTextResolverInterface $queryTextResolver,
        ResultSorter $resultSorter,
        ConnectionManagerInterface $connectionManager,
        EngineResolver $engineResolver
    ) {
        $this->queryTextResolver = $queryTextResolver;
        $this->client            = $connectionManager->getClient();
        $this->resultSorter      = $resultSorter;
        $this->engineResolver    = $engineResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSearchParams(RequestInterface $request, array $searchParams): array
    {
        $queryText = $this->queryTextResolver->getText($request);

        if (!empty($queryText)) {
            $exactMatchIds = $this->getExactMatchIds($request, $queryText);

            if (count($exactMatchIds)) {
                $filter = ['id' => $exactMatchIds];

                if (isset($searchParams['filters'])) {
                    $filter = ['all' => [$searchParams['filters'], $filter]];
                }

                $searchParams['filters'] = $filter;
            }
        }

        return $searchParams;
    }

    /**
     * {@inheritDoc}
     */
    public function rescoreResults(RequestInterface $request, array $results): array
    {
        if ($this->canRescore($request)) {
            $docScores = array_filter(array_map([$this, 'getDocScore'], $results));
            if (!empty($docScores)) {
                $maxScore = max($docScores);
                $currentPosition = 0;
                while ($this->getDocScore($results[$currentPosition]) < $maxScore) {
                    $scorePosition = (count($results) - $currentPosition) * self::RESCORE_INCREMENT + $maxScore;
                    $results[$currentPosition]['_meta']['score'] = $scorePosition;
                    $currentPosition++;
                }

                $this->resultSorter->sortResults($results, $this->getSortDirection($request));
            }
        }

        return $results;
    }

    /**
     * Check if the current request need to be rescored.
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function canRescore(RequestInterface $request): bool
    {
        return count($request->getSort()) < 1 || current($request->getSort())->getField() == '_score';
    }

    /**
     * Extract score from a document.
     *
     * @param array $doc
     *
     * @return float
     */
    private function getDocScore(array $doc): float
    {
        return (float) $doc['_meta']['score'];
    }

    /**
     * Retrieve current sort direction from the request.
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    private function getSortDirection(RequestInterface $request): string
    {
        return count($request->getSort()) < 1 ? 'DESC' : current($request->getSort())->getDirection();
    }

    /**
     * Try to detect if fuzzy search result are present in the first 100 results.
     * If so, return only non fuzzy results.
     *
     * @param RequestInterface $request
     * @param string           $queryText
     * @param array            $searchParams
     *
     * @return string[]
     */
    private function getExactMatchIds(RequestInterface $request, string $queryText)
    {
        $engineName   = $this->engineResolver->getEngine($request)->getName();
        $searchParams = [
          PageParams::PAGE_PARAM_NAME => [
            PageParams::PAGE_SIZE_PARAM_NAME => PageParams::MAX_PAGE_SIZE
          ],
          AnalytcisParams::ANALYTICS_PARAM_NAME => [
            AnalytcisParams::TAGS_PARAM_NAME => [self::ANALYTICS_TAG]
          ],
        ];

        $searchResponse = $this->client->search($engineName, $queryText, $searchParams);

        $totalResult       = count($searchResponse['results']);
        $exactMatchResults = array_filter($searchResponse['results'], [$this, 'isExactMatch']);

        $useMatchedDocIds = count($exactMatchResults) && $totalResult > count($exactMatchResults);

        return $useMatchedDocIds ? array_map([$this, 'getDocId'], $exactMatchResults) : [];
    }

    /**
     * Indicate if a search result is fuzzy.
     *
     * @param array $doc
     *
     * @return boolean
     */
    private function isExactMatch(array $doc)
    {
        return $doc['_meta']['score'] > 0.1;
    }

    /**
     * Retrive id of a document.
     *
     * @param array $doc
     *
     * @return string
     */
    private function getDocId(array $doc)
    {
        return $doc['id']['raw'];
    }
}
