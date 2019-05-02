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

/**
 * Ensure score is consistent with the document positions.
 * Mostly intended to fix curated search results scores.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Rescorer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DefaultRescorer implements RescorerInterface
{
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
     * @param ResultSorter $resultSorter
     */
    public function __construct(ResultSorter $resultSorter)
    {
        $this->resultSorter = $resultSorter;
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
     * {@inheritDoc}
     */
    public function prepareSearchParams(RequestInterface $request, array $searchParams): array
    {
        return $searchParams;
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
}
