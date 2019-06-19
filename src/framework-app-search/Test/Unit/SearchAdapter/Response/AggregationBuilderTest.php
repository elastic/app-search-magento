<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\Response;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\Response\AggregationBuilder;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\BucketInterface;
use \Magento\Framework\Api\Search\AggregationValueInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Search\Response\Aggregation;
use Magento\Framework\Search\Response\Bucket;
use Magento\Framework\Search\Response\Aggregation\Value as AggregationValue;

/**
 * Unit test for the AggregationBuilder class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\Response
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class AggregationBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test valid aggregations are returned.
     *
     * @dataProvider rawFacetsDataProvider
     */
    public function testCreateDocument($response)
    {
        $aggregations = $this->getAggregationBuilder()->getAggregations($response);

        $this->assertInstanceOf(AggregationInterface::class, $aggregations);
        $facets = $response['facets'] ?? [];

        $this->assertCount(count($facets), $aggregations->getBuckets());

        foreach ($facets as $facetName => $facetData) {
            $bucket = $aggregations->getBucket($facetName);
            $this->assertInstanceOf(BucketInterface::class, $bucket);
            $this->assertEquals($facetName, $bucket->getName());
            $this->assertCount(count($facetData), $bucket->getValues());
        }
    }

    public function rawFacetsDataProvider()
    {
        $rawFacets = [
          [
            [],
          ],
          [
            [
              'facets' => [],
            ],
          ],
          [
            [
              'facets' => [
                'emptybucket' => [],
              ],
            ],
          ],
          [
            [
              'facets' => [
                'bucket' => [['value' => 'foo', 'count' => 1], ['value' => 'bar', 'count' => 2]],
              ]
            ],
          ],
          [
            [
              'facets' => [
                'bucket1' => [['value' => 'foo', 'count' => 1]],
                'bucket2' => [['value' => 'bar', 'count' => 2]],
              ],
            ],
          ],
        ];

        return $rawFacets;
    }

    /**
     * Aggregation builder used during tests.
     *
     * @return AggregationBuilder
     */
    private function getAggregationBuilder(): AggregationBuilder
    {
        $objectManager = new ObjectManager($this);

        $constructorArgs = $objectManager->getConstructArguments(AggregationBuilder::class);

        $constructorArgs['aggregationFactory']->expects($this->once())->method('create')
          ->willReturnCallback($this->getCreateObjectStub($objectManager, Aggregation::class));

        $constructorArgs['bucketFactory']->method('create')
          ->willReturnCallback($this->getCreateObjectStub($objectManager, Bucket::class));

        $constructorArgs['aggregationValueFactory']->method('create')
          ->willReturnCallback($this->getCreateObjectStub($objectManager, AggregationValue::class));

        return $objectManager->getObject(AggregationBuilder::class, $constructorArgs);
    }

    /**
     * Instantiate a factory for a class.
     *
     * @param ObjectManager $objectManager
     * @param string        $className
     *
     * @return callable
     */
    private function getCreateObjectStub(ObjectManager $objectManager, string $className): callable
    {
        return function ($data) use ($objectManager, $className) {
            return $objectManager->getObject($className, $data);
        };
    }
}
