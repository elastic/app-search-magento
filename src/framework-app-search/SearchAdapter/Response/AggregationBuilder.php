<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\Response;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Search\Response\AggregationFactory;
use Magento\Framework\Api\Search\BucketInterface;
use Magento\Framework\Search\Response\BucketFactory;
use Magento\Framework\Search\Response\Aggregation\ValueFactory as AggregationValueFactory;
use Magento\Framework\Search\Response\Aggregation\Value as AggregationValue;

/**
 * Build response aggregation from the App Search response.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\Response
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AggregationBuilder
{
    /**
     * @var AggregationFactory
     */
    private $aggregationFactory;

    /**
     * @var BucketFactory
     */
    private $bucketFactory;

    /**
     * @var AggregationValueFactory
     */
    private $aggregationValueFactory;

    /**
     * Constructor.
     *
     * @param AggregationFactory      $aggregationFactory
     * @param BucketFactory           $bucketFactory
     * @param AggregationValueFactory $aggregationValueFactory
     */
    public function __construct(
        AggregationFactory $aggregationFactory,
        BucketFactory $bucketFactory,
        AggregationValueFactory $aggregationValueFactory
    ) {
        $this->aggregationFactory      = $aggregationFactory;
        $this->bucketFactory           = $bucketFactory;
        $this->aggregationValueFactory = $aggregationValueFactory;
    }

    /**
     * Extract aggregations from the App Search raw response.
     *
     * @param array $rawResponse
     *
     * @return AggregationInterface
     */
    public function getAggregations(array $rawResponse): AggregationInterface
    {
        $buckets = [];

        foreach ($rawResponse['facets'] ?? [] as $bucketName => $facetData) {
            $bucketData = ['name' => $bucketName, 'values' => $this->getAggregationValues($facetData)];
            $buckets[$bucketName] = $this->bucketFactory->create($bucketData);
        }

        return $this->aggregationFactory->create(['buckets' => $buckets]);
    }

    /**
     * Build aggregation values from facet values.
     *
     * @param array $facetData
     *
     * @return AggregationValue[]
     */
    private function getAggregationValues(array $facetData): array
    {
        $values = [];

        foreach ($facetData as $facetValue) {
            $valueData = ['value' => $facetValue['value'], 'metrics' => $facetValue];
            $values[] = $this->aggregationValueFactory->create($valueData);
        }

        return $values;
    }
}
