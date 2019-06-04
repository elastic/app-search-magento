<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Document;

use Elastic\AppSearch\Model\Adapter\EngineInterface;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Elastic\AppSearch\Client\ConnectionManager;
use Elastic\AppSearch\Client;

/**
 * Implementation of the sync manager component.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\Model\Adapter\Document
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SyncManager implements SyncManagerInterface
{
    /**
     * @var int
     */
    private const DEFAULT_BATCH_SIZE = 100;

    /**
     * @var array
     */
    private $docs   = [];

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var BatchDataMapperResolverInterface
     */
    private $batchDataMapperResolver;

    /**
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param BatchDataMapperResolverInterface $batchDataMapperResolver
     * @param ConnectionManager                $connectionManager
     * @param Batch                            $batch
     * @param int                              $batchSize
     */
    public function __construct(
        BatchDataMapperResolverInterface $batchDataMapperResolver,
        ConnectionManager $connectionManager,
        Batch $batch,
        int $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
        $this->batchDataMapperResolver = $batchDataMapperResolver;
        $this->client                  = $connectionManager->getClient();
        $this->batch                   = $batch;
        $this->batchSize               = $batchSize;
    }

    /**
     * {@inheritDoc}
     */
    public function addDocuments(EngineInterface $engine, \Traversable $documents)
    {
        if (!isset($this->docs[$engine->getName()])) {
            $this->docs[$engine->getName()] = [];
        }

        $batchDataMapper = $this->batchDataMapperResolver->getMapper($engine->getIdentifier());

        foreach ($this->batch->getItems($documents, $this->batchSize) as $docs) {
            $documents = $batchDataMapper->map($docs, $engine->getStoreId());
            foreach ($documents as $doc) {
                $this->docs[$engine->getName()][$doc['id']] = $doc;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocuments(EngineInterface $engine, \Traversable $documents)
    {
        foreach ($this->batch->getItems($documents, $this->batchSize) as $entityIds) {
            $documents = array_map(
                function ($docId) {
                    return ["id" => $docId, "deleted" => true];
                },
                $entityIds
            );

            foreach ($documents as $doc) {
                $this->docs[$engine->getName()][$doc['id']] = $doc;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sync()
    {
        foreach ($this->docs as $engineName => $docs) {
            foreach (array_chunk($docs, $this->batchSize) as $insertDocs) {
                $this->client->indexDocuments($engineName, $insertDocs);
            }
        }

        $this->docs = [];
    }
}
