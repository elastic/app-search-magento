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

/**
 * ResultSorter used by rescorer to rearrange doc list after rescoring.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Rescorer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ResultSorter
{
    /**
     * Sort results set by score and id.
     *
     * @param array  $results
     * @param string $dir
     *
     * @return array
     */
    public function sortResults(array $results, string $dir): array
    {
        usort($results, [$this, 'compareDoc']);

        return $dir == 'DESC' ? array_reverse($results) : $results;
    }

    /**
     * Comparator function used to compore 2 docs while sorting.
     *
     * @param array $doc1
     * @param array $doc2
     *
     * @return number
     */
    private function compareDoc(array $doc1, array $doc2): int
    {
        $result = $this->getDocScore($doc1) - $this->getDocScore($doc2);

        if ($result === 0 && is_numeric($this->getDocId($doc1)) && is_numeric($this->getDocId($doc2))) {
            $result = $this->getDocId($doc1) - $this->getDocId($doc2);
        } elseif ($result === 0) {
            $result = strcmp($this->getDocId($doc1), $this->getDocId($doc2));
        }

        return  $result < 0 ? -1 : 1;
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
     * Extract id from a document.
     *
     * @param array $doc
     *
     * @return string
     */
    private function getDocId(array $doc): string
    {
        return (string) $doc['entity_id']['raw'];
    }
}
