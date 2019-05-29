<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Rescorer;

use Elastic\AppSearch\SearchAdapter\Request\RescorerInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Client\ConnectionManager;
use Elastic\AppSearch\SearchAdapter\Request\EngineResolver;
use Elastic\AppSearch\SearchAdapter\Request\Analytics\SearchParamsProvider as AnalyticsSearchParams;
use Elastic\AppSearch\SearchAdapter\Request\Page\SearchParamsProvider as PageSearchParams;

/**
 * Ensure score is consistent with the document positions.
 * Mostly intended to fix curated search results scores.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Rescorer
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
     * @var ResultSorter
     */
    private $resultSorter;

    /**
     * Constructor.
     *
     * @param ResultSorter      $resultSorter
     * @param ConnectionManager $connectionManager
     * @param EngineResolver    $engineResolver
     */
    public function __construct(
        ResultSorter $resultSorter,
        ConnectionManager $connectionManager,
        EngineResolver $engineResolver
    ) {
        $this->client         = $connectionManager->getClient();
        $this->resultSorter   = $resultSorter;
        $this->engineResolver = $engineResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSearchParams(RequestInterface $request, string $queryText, array $searchParams): array
    {
        $exactMatchIds = $this->getExactMatchIds($request, $queryText);

        if (count($exactMatchIds)) {
            $filter = ['entity_id' => $exactMatchIds];

            if (isset($searchParams['filters'])) {
                $filter = ['all' => [$searchParams['filters'], $filter]];
            }

            $searchParams['filters'] = $filter;
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
          PageSearchParams::PAGE_PARAM_NAME => [
            PageSearchParams::PAGE_SIZE_PARAM_NAME => PageSearchParams::MAX_PAGE_SIZE
          ],
          AnalyticsSearchParams::ANALYTICS_PARAM_NAME => [
            AnalyticsSearchParams::TAGS_PARAM_NAME => [self::ANALYTICS_TAG]
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
        return $doc['entity_id']['raw'];
    }
}
