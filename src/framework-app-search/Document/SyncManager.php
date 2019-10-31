<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Document;

use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Elastic\AppSearch\Framework\AppSearch\Client\ConnectionManagerInterface;
use Elastic\AppSearch\Client\Client;
use Ramsey\Uuid\UuidFactory;

/**
 * Implementation of the sync manager component.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Document
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
     * @var int
     */
    private const MAX_SYNC_TRIES = 20;

    /**
     * @var string
     */
    private const SEARCH_ANALYTICS_TAG = 'sync_manager';

    /**
     * @var array
     */
    private $docs = [];

    /**
     * @var string
     */
    private $syncId;

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
     * @var UuidFactory
     */
    private $uuidFactory;

    /**
     * Constructor.
     *
     * @param BatchDataMapperResolverInterface $batchDataMapperResolver
     * @param ConnectionManagerInterface       $connectionManager
     * @param Batch                            $batch
     * @param UuidFactory                      $uuidFactory
     * @param int                              $batchSize
     */
    public function __construct(
        BatchDataMapperResolverInterface $batchDataMapperResolver,
        ConnectionManagerInterface $connectionManager,
        Batch $batch,
        UuidFactory $uuidFactory,
        int $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
        $this->batchDataMapperResolver = $batchDataMapperResolver;
        $this->client                  = $connectionManager->getClient();
        $this->batch                   = $batch;
        $this->batchSize               = $batchSize;
        $this->uuidFactory             = $uuidFactory;
        $this->syncId                  = $uuidFactory->uuid4()->toString();
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
                $doc['sync_id'] = $this->syncId;
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
                    return ['id' => $docId, 'deleted' => true, 'sync_id' => $this->syncId];
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
    public function deleteAllDocuments(EngineInterface $engine)
    {
        $currentPage = 1;
        do {
            $docList = $this->client->listDocuments($engine->getName(), $currentPage, 100);
            $this->deleteDocuments($engine, $this->prepareDeleteDocIds($docList['results']));
            $currentPage++;
        } while ($currentPage <= $docList['meta']['page']['total_pages']);
    }

    /**
     * {@inheritDoc}
     */
    public function sync()
    {
        foreach ($this->docs as $engineName => $docs) {
            $indexedDocs = 0;
            foreach (array_chunk($docs, $this->batchSize) as $insertDocs) {
                $resp = $this->client->indexDocuments($engineName, $insertDocs);
                $indexedDocs += count(array_filter($resp, function ($doc) {
                    return empty($doc['errors']);
                }));
            }

            $this->waitForSync($engineName, $indexedDocs);
        }

        $this->docs   = [];
        $this->syncId = $this->uuidFactory->uuid4()->toString();
    }

    /**
     * {@inheritDoc}
     */
    public function purgeDeletedDocuments(EngineInterface $engine)
    {
        foreach (array_chunk($this->getDeletedDocumentIds($engine), $this->batchSize) as $docIds) {
            $this->client->deleteDocuments($engine->getName(), $docIds);
        }
    }

    /**
     * Wait for the engine to be synced and all update to be searchable.
     *
     * @param string $engineName
     * @param int    $expectedCount
     */
    private function waitForSync(string $engineName, int $expectedCount)
    {
        if ($expectedCount > 0) {
            $searchParams = [
                'filters'   => ['sync_id' => $this->syncId],
                'page'      => ['current' => 1, 'size' => 0],
                'analytics' => ['tags' => [self::SEARCH_ANALYTICS_TAG]],
            ];

            $attempts = self::MAX_SYNC_TRIES;

            do {
                if ($attempts != self::MAX_SYNC_TRIES) {
                    usleep(200);
                }
                $resp = $this->client->search($engineName, '', $searchParams);
                $attempts --;
            } while ($attempts > 0 && $resp['meta']['page']['total_results'] < $expectedCount);
        }
    }

    /**
     * Return a list of product that are marked for deletion into the engine.
     *
     * @param EngineInterface $engine
     *
     * @return array
     */
    private function getDeletedDocumentIds(EngineInterface $engine): array
    {
        $docIds      = [];
        $currentPage = 1;

        do {
            $searchParams = [
                'filters' => ['deleted' => "true"],
                'page'    => ['current' => $currentPage, 'size' => 100],
                'analytics' => ['tags' => [self::SEARCH_ANALYTICS_TAG]],
            ];

            $resp = $this->client->search($engine->getName(), '', $searchParams);
            foreach ($resp['results'] as $doc) {
                $docIds[] = $doc['id']['raw'];
            }
            $currentPage++;
        } while (!empty($resp['results']));

        return $docIds;
    }

    /**
     * Transfor array of doc into generator of doc ids.
     *
     * @param array $docs
     *
     * @return \Generator
     */
    private function prepareDeleteDocIds(array $docs)
    {
        foreach ($docs as $doc) {
            yield $doc['id'];
        }
    }
}
