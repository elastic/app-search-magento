<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Facet\Algorithm;

use Elastic\AppSearch\SearchAdapter\RequestExecutor\Response\Facet\Algorithm\Auto;

/**
 * Unit test for the Elastic\AppSearch\SearchAdapter\RequestExecutor\Response\Facet\Algorithm\Auto
 *
 * @package   Elastic\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Facet\Algorithm;
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class AutoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $rangeSamples = [
        [
            [[10, 20, 1], [20, 30, 3], [30, 40, 4], [40, 50, 3]],
            [[10, 20, 1], [20, 30, 3], [30, 40, 4], [40, 50, 3]],
        ],
        [
            [[10, 20, 20], [20, 30, 30], [30, 40, 20], [40, 50, 5], [110, 120, 2]],
            [[10, 20, 20], [20, 30, 30], [30, 40, 20], [40, 50, 5], [50, null, 2]],
        ],
        [
            [[10, 20, 210000], [20, 30, 100000], [30, 40, 100000], [40, 50, 100000], [110, 120, 1]],
            [[10, 20, 210000], [20, 30, 100000], [30, 40, 100000], [40, 50, 100000], [50, null, 1]],
        ],
        [
            [[10, 20, 210000], [20, 30, 100000], [30, 40, 100000], [40, 50, 100000], [1000, null, 1]],
            [[10, 20, 210000], [20, 30, 100000], [30, 40, 100000], [40, 50, 100000], [50, null, 1]],
        ]
    ];

    /**
     * Iterate over samples to test range generation.
     *
     * @dataProvider rangesDataProvider
     *
     * @param array $rangeInput
     * @param array $rangeOutput
     */
    public function testGetRanges(array $rangeInput, array $rangeOutput)
    {
        $algorithm = new Auto();
        $this->assertEquals($rangeOutput, $algorithm->getRanges($rangeInput));
    }

    /**
     * Return ranges used during tests.
     *
     * @return array
     */
    public function rangesDataProvider(): array
    {
        return array_map(function ($sample) {
            return array_map(function ($ranges) {
                return array_map(function ($range) {
                    return array_filter(
                        ['from' => $range[0], 'to' => $range[1], 'count' => $range[2]],
                        function ($value) {
                            return $value !== null;
                        }
                    );
                }, $ranges);
            }, $sample);
        }, $this->rangeSamples);
    }
}
