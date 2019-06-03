<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\RequestExecutor\Response\Facet\Algorithm;

use Elastic\AppSearch\SearchAdapter\RequestExecutor\Response\Facet\AlgorithmInterface;

/**
 * Automatic dynamic range facets value algorithm.
 *
 * @package   Elastic\AppSearch\SearchAdapter\RequestExecutor\Response\Facet\Algorithm
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Auto implements AlgorithmInterface
{
    /**
     * {@inheritDoc}
     */
    public function getRanges(array $data): array
    {
        $stats = $this->getStats($data);

        $validIntervals = array_filter($data, function ($range) use ($stats) {
            return ($range["to"] ?? $range["from"]) < $stats['avg'] + 3 * $stats['stdDev'];
        });

        $stats = $this->getStats($validIntervals);
        $intervalSize = (int) pow(10, max(1, strlen((int) $stats['stdDev']) - 1));

        $intervals = $this->applyIntervalSize($data, $intervalSize, $stats);

        return $intervals;
    }

    /**
     * Apply new interval size to the list of intervals.
     * Value outside of the bounds are grouped togther into the last entry.
     *
     * @param array $intervals
     * @param int   $intervalSize
     * @param array $bounds
     *
     * @return array
     */
    private function applyIntervalSize(array $intervals, int $intervalSize, array $bounds): array
    {
        $result = [];
        $maxBound = (int) ceil($bounds['max'] / $intervalSize) * $intervalSize;
        foreach ($intervals as $interval) {
            $from  = (int) floor($interval['from'] / $intervalSize) * $intervalSize;
            if ($from >= $maxBound) {
                $from = $maxBound;
            }
            $count = isset($result[$from]) ? $result[$from]['count'] + $interval['count'] : $interval['count'];
            $result[$from] = ['from'  => (int) $from, 'count' => (int) $count];
            if ($from < $maxBound) {
                $result[$from]['to'] = $from + $intervalSize;
            }
        }

        return array_values($result);
    }

    /**
     * Compute stats for a list of intervals.
     *
     * @param array $intervals
     * @return number[]
     */
    private function getStats(array $intervals): array
    {
        $avg     = $this->getAvg($intervals);
        $stdDev  = $this->getStdDev($avg, $intervals);
        $min     = (int) min(array_column($intervals, 'from'));
        $max     = (int) max(array_column($intervals, 'to'));

        return ['avg' => $avg, 'stdDev' => $stdDev, 'min' => $min, 'max' => $max];
    }

    /**
     * Compute std dev for a list of interval.
     *
     * @param float $avg
     * @param array $samples
     *
     * @return number
     */
    private function getStdDev(float $avg, array $intervals)
    {
        $distSum = array_sum(array_map(function ($interval) use ($avg) {
            $currentValue = (($interval['to'] ?? $interval['from']) + $interval['from']) / 2;
            return $interval['count'] * pow($currentValue - $avg, 2);
        }, $intervals));

        return sqrt($distSum / array_sum(array_column($intervals, 'count')));
    }

    /**
     * Coupute average for a list if intervals.
     *
     * @param array $intervals
     *
     * @return number
     */
    private function getAvg(array $intervals)
    {
        $sum = array_sum(array_map(function ($interval) {
            return $interval['count'] * (($interval['to'] ?? $interval['from']) + $interval['from']) / 2;
        }, $intervals));

        return $sum / array_sum(array_column($intervals, 'count'));
    }
}
