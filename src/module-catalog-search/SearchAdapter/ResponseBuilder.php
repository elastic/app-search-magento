<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter;

use Magento\Framework\Search\ResponseInterface;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Response\DocumentFactory;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Response\AggregationBuilder;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Response\DocumentCountResolver;

/**
 * AppSearch search adapter response factory implementation.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ResponseBuilder
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var AggregationBuilder
     */
    private $aggregationBuilder;

    /**
     * @var DocumentCountResolver
     */
    private $documentCountResolver;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param ResponseFactory         $responseFactory
     * @param DocumentFactory         $documentFactory
     * @param AggregationBuilder      $aggregationBuilder
     * @param DocumentCountResolver   $documentCountResolver
     */
    public function __construct(
        ResponseFactory $responseFactory,
        DocumentFactory $documentFactory,
        AggregationBuilder $aggregationBuilder,
        DocumentCountResolver $documentCountResolver
    ) {
        $this->responseFactory       = $responseFactory;
        $this->documentFactory       = $documentFactory;
        $this->aggregationBuilder    = $aggregationBuilder;
        $this->documentCountResolver = $documentCountResolver;
    }

    /**
     * Build a search response from raw data returned by App Search.
     *
     * @param array $rawResponse
     *
     * @return ResponseInterface
     */
    public function buildResponse(array $rawResponse): ResponseInterface
    {
        $responseData = [
            'documents'    => array_map([$this->documentFactory, 'create'], $rawResponse['results'] ?? []),
            'aggregations' => $this->aggregationBuilder->getAggregations($rawResponse),
            'count'        => $this->documentCountResolver->getDocumentCount($rawResponse),
        ];

        return $this->responseFactory->create($responseData);
    }
}
