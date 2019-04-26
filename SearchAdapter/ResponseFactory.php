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
     * Constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param DocumentFactory        $documentFactory
     * @param AggregationFactory     $aggregationFactory
     * @param string                 $instance
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory,
        string $instance = self::CLASS_NAME
    ) {
        $this->documentFactory    = $documentFactory;
        $this->aggregationFactory = $aggregationFactory;
        $this->objectManager      = $objectManager;
        $this->instance           = $instance;
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param array $rawResponse
     *
     * @return AggregationInterface
     */
    private function getAggregations(array $rawResponse): AggregationInterface
    {
        return $this->aggregationFactory->create(['buckets' => []]);
    }
}
