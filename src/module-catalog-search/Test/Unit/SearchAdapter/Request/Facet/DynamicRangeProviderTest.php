<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Request\Facet;

use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet\DynamicRangeProvider;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\Aggregation\Range;
use Magento\Framework\Search\Request\Aggregation\RangeFactory;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet\DynamicRangeProvider class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Request\Facet
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class DynamicRangeProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test automated interval generation:
     *   - Interval must have the Range::class
     *   - Interval should be continous with no overlaping
     *   - Should start at 0 and finish at 1,000,000
     *   - Check each interval size
     */
    public function testDynamicRangeProvider()
    {
        $bucket = $this->createMock(BucketInterface::class);

        $dynamicRangeProvider = new DynamicRangeProvider($this->getRangeFactory());

        $ranges = $dynamicRangeProvider->getRanges($bucket);

        $from = 0;
        foreach ($ranges as $range) {
            $this->assertInstanceOf(Range::class, $range);
            $this->assertEquals($from, $range->getFrom());
            $expectedSize = pow(10, max(1, strlen($range->getFrom()) - 2));
            $this->assertEquals($expectedSize, $range->getTo() - $range->getFrom());
            $from = $range->getTo();
        }


        $this->assertEquals(pow(10, 6), end($ranges)->getTo());
    }

    /**
     * Mock range factory used during test.
     *
     * @return RangeFactory
     */
    private function getRangeFactory()
    {
        $createMethod = function ($range) {
            return new Range($range['from'], $range['to']);
        };

        $rangeFactory = $this->createMock(RangeFactory::class);
        $rangeFactory->method('create')->will($this->returnCallback($createMethod));

        return $rangeFactory;
    }
}
