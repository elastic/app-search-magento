<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter;

use Magento\Framework\Search\ResponseInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Search\Response\AggregationFactory;
use Elastic\AppSearch\SearchAdapter\Response\DocumentFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\BucketInterface;
use Magento\Framework\Search\Response\BucketFactory;
use Magento\Framework\Search\Response\Aggregation\ValueFactory as AggregationValueFactory;
use Magento\CatalogSearch\Model\Search\RequestGenerator;


/**
 * AppSearch search adapter response factory implementation.
 *
 * @package   Elastic\AppSearch\SearchAdapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ResponseFactory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = Response::class;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var AggregationFactory
     */
    private $aggregationFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $instance;

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
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param ObjectManagerInterface  $objectManager
     * @param DocumentFactory         $documentFactory
     * @param AggregationFactory      $aggregationFactory
     * @param BucketFactory           $bucketFactory
     * @param AggregationValueFactory $aggregationValueFactory
     * @param string                  $instance
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory,
        BucketFactory $bucketFactory,
        AggregationValueFactory $aggregationValueFactory,
        string $instance = self::CLASS_NAME
    ) {
        $this->documentFactory         = $documentFactory;
        $this->aggregationFactory      = $aggregationFactory;
        $this->objectManager           = $objectManager;
        $this->instance                = $instance;
        $this->bucketFactory           = $bucketFactory;
        $this->aggregationValueFactory = $aggregationValueFactory;
    }

    /**
     * Build a search response from raw data returned by App Search.
     *
     * @param array $rawResponse
     *
     * @return ResponseInterface
     */
    public function create(array $rawResponse): ResponseInterface
    {
        $responseData = [
            'documents'    => $this->getDocuments($rawResponse),
            'aggregations' => $this->getAggregations($rawResponse),
            'count'        => $this->getDocumentsCount($rawResponse),
        ];

        return $this->objectManager->create($this->instance, $responseData);
    }

    /**
     * Extract document count from the App Search raw response.
     *
     * @param array $rawResponse
     *
     * @return int
     */
    private function getDocumentsCount(array $rawResponse): int
    {
        return (int) $rawResponse['meta']['page']['total_results'];
    }

    /**
     * Extract documents from the App Search raw response.
     *
     * @param array $rawResponse
     *
     * @return DocumentInterface[]
     */
    private function getDocuments(array $rawResponse): array
    {
        return array_map([$this->documentFactory, 'create'], $rawResponse['results'] ?? []);
    }

    /**
     * Extract aggregations from the App Search raw response.
     *
     * @param array $rawResponse
     *
     * @return AggregationInterface
     */
    private function getAggregations(array $rawResponse): AggregationInterface
    {
        $buckets = [];

        foreach ($rawResponse['facets'] ?? [] as $facetName => $rawValues) {
            $values = [];
            foreach ($rawValues as $value) {
                $valueData = ['value' => $value['value'], 'metrics' => $value];
                $values[] = $this->aggregationValueFactory->create($valueData);
            }
            $buckets[$facetName] = $this->bucketFactory->create(['name' => $facetName, 'values' => $values]);
        }

        $resultCountBucket = $this->getResultCountBucket($rawResponse);

        $buckets[$resultCountBucket->getName()] = $resultCountBucket;

        return $this->aggregationFactory->create(['buckets' => $buckets]);
    }

    /**
     * Temporary fix to allow having search results count available through aggregations.
     *
     * @return BucketInterface
     */
    private function getResultCountBucket(array $rawResponse): BucketInterface
    {
        $docCounts = $this->getDocumentsCount($rawResponse);

        $valueData = ['value' => 'docs', 'metrics' => ['value' => 'docs', 'count' => $docCounts]];
        $values = [$this->aggregationValueFactory->create($valueData)];

        return $this->bucketFactory->create(['name' => '_meta' . RequestGenerator::BUCKET_SUFFIX, 'values' => $values]);
    }
}
