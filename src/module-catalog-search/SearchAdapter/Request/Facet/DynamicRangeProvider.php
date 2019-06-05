<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet;

use Magento\Framework\Search\Request\Aggregation\Range;
use Magento\Framework\Search\Request\Aggregation\RangeFactory;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Build ranges used to build dynamic aggregation facets.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DynamicRangeProvider
{
    /**
     * @var int
     */
    private const DEFAULT_MAX_POW = 6;

    /**
     * @var RangeFactory
     */
    private $rangeFactory;

    /**
     * @var int
     */
    private $maxPow;

    /**
     * Constructor.
     *
     * @param RangeFactory $rangeFactory
     * @param int          $maxPow
     */
    public function __construct(
        RangeFactory $rangeFactory,
        int $maxPow = self::DEFAULT_MAX_POW
    ) {
        $this->rangeFactory = $rangeFactory;
        $this->maxPow       = $maxPow;
    }

    /**
     * Build bucket ranges.
     *
     * TODO: Add support for manual step config.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param BucketInterface $bucket
     *
     * @return Range[]
     */
    public function getRanges(BucketInterface $bucket): array
    {
        return $this->getAutomaticRanges();
    }

    /**
     * Build automatic interval that will be reworked by post processing.
     *
     * @return Range[]
     */
    private function getAutomaticRanges(): array
    {
        $ranges = [];
        $from   = 0;

        foreach (range(3, $this->maxPow) as $pow) {
            $intervalSize = pow(10, $pow - 2);
            $upperLmit = pow(10, $pow);

            while ($from + $intervalSize <= $upperLmit) {
                $to = $from + $intervalSize;
                $ranges[] = $this->rangeFactory->create(['from' => $from, 'to' => $to]);
                $from = $to;
            }
        }

        return $ranges;
    }
}
