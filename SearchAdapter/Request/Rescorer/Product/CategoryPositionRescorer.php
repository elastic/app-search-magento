<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Rescorer\Product;

use Elastic\AppSearch\SearchAdapter\Request\RescorerInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\SearchAdapter\Request\Rescorer\ResultSorter;

/**
 * Rescore top 100 positioned products for the current searched category.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\PositionedDocuments
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CategoryPositionRescorer implements RescorerInterface
{
    /**
     * @var CategoryPositionProvider
     */
    private $positionProvider;

    /**
     * @var ResultSorter
     */
    private $resultSorter;

    /**
     * Constructor.
     *
     * @param CategoryPositionProvider $positionProvider
     * @param ResultSorter             $resultSorter
     */
    public function __construct(CategoryPositionProvider $positionProvider, ResultSorter $resultSorter)
    {
        $this->positionProvider = $positionProvider;
        $this->resultSorter     = $resultSorter;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSearchParams(RequestInterface $request, string $queryText, array $searchParams): array
    {
        if ($this->canRescore($request)) {
            $positionSlots = $this->getPositionSlots($request);

            foreach ($positionSlots as $slotIndex => $slotProductIds) {
                $currentBoost = (count($positionSlots) - $slotIndex) / count($positionSlots);
                $searchParams['boosts']['entity_id'][] = [
                    'type'   => 'value',
                    'value'  => $slotProductIds,
                    'factor' => $currentBoost,
                ];
            }
        }

        return $searchParams;
    }

    /**
     * {@inheritDoc}
     */
    public function rescoreResults(RequestInterface $request, array $results): array
    {
        $positionedProductIds = array_intersect(
            $this->getPositionedDocuments($request),
            array_map([$this, 'getDocumentId'], $results)
        );

        foreach ($results as &$currentResult) {
            $docId = $this->getDocumentId($currentResult);
            $positionedIndex = array_search($docId, $positionedProductIds);
            $scoreIncrement  = 1 / count($results);
            if ($positionedIndex !== false) {
                $originalScore = $currentResult['_meta']['score'];
                $positionScore = (count($positionedProductIds) - $positionedIndex) * $scoreIncrement;
                $currentResult['_meta']['score'] = $originalScore + $positionScore;
            }
        }

        $results = $this->resultSorter->sortResults($results, $this->getSortDirection($request));

        return $results;
    }

    private function getDocumentId(array $doc): string
    {
        return $doc['entity_id']['raw'];
    }

    private function getSortDirection(RequestInterface $request): string
    {
        return count($request->getSort()) < 1 ? 'DESC' : current($request->getSort())->getDirection();
    }

    private function getPositionedDocuments(RequestInterface $request): array
    {
        return $this->positionProvider->getPositionedDocuments($request);
    }

    private function getPositionSlots(RequestInterface $request): array
    {
        return array_chunk($this->getPositionedDocuments($request), $request->getSize());
    }

    private function canRescore(RequestInterface $request): bool
    {
        return count($request->getSort()) < 1 || current($request->getSort())->getField() == '_score';
    }
}
