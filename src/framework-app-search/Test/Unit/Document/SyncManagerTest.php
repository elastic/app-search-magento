<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\Document;

use Elastic\AppSearch\Framework\AppSearch\Document\SyncManager;
use Elastic\AppSearch\Framework\AppSearch\Document\BatchDataMapperResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Document\BatchDataMapperInterface;
use Elastic\AppSearch\Framework\AppSearch\Client\ConnectionManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Elastic\AppSearch\Client\Client;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Ramsey\Uuid\UuidFactory;

/**
 * Unit test for the SyncManager class.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\Document
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SyncManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $writtenDocs = [];

    /**
     * Test adding docs to an engine using sync manager.
     *
     * @param int $docCount
     * @param int $batchSize
     * @param int $countEngine
     */
    public function testAddDocuments(int $docCount = 200, int $batchSize = 10, int $countEngine = 2)
    {
        $this->runOperartionTest(
            [$this, 'getAddedDocuments'],
            'addDocuments',
            $docCount,
            $batchSize,
            $countEngine
        );
    }

    /**
     * Test deleting docs to an engine using sync manager.
     *
     * @param int $docCount
     * @param int $batchSize
     * @param int $countEngine
     */
    public function testDeleteDocuments(int $docCount = 200, int $batchSize = 10, int $countEngine = 2)
    {
        $this->runOperartionTest(
            [$this, 'getDeletedDocuments'],
            'deleteDocuments',
            $docCount,
            $batchSize,
            $countEngine
        );
    }

    /**
     * Run testing for a specific operation (add or delete doc).
     *
     * @param callable $docSource
     * @param string   $operationName
     * @param string   $docCount
     * @param string   $batchSize
     * @param string   $countEngine
     */
    private function runOperartionTest(
        callable $docSource,
        string $operationName,
        int $docCount,
        int $batchSize,
        int $countEngine
    ) {
        $expectedBatchCount = intval($docCount / $batchSize) * $countEngine;

        $syncManager = $this->getSyncManager($batchSize, $this->getClientMock($expectedBatchCount));

        for ($i = 0; $i < $countEngine; $i++) {
            $docs   = $docSource(1, $docCount);
            $engine = $this->createMock(EngineInterface::class);
            $engine->method('getName')->willReturn('engine-' . $i);
            $syncManager->$operationName($engine, $docs);
        }

        $syncManager->sync();

        $this->assertCount($countEngine, $this->writtenDocs);
        for ($i = 0; $i < $countEngine; $i++) {
            $this->assertArrayHasKey('engine-' . $i, $this->writtenDocs);
            $this->assertCount($docCount, $this->writtenDocs['engine-' . $i]);
        }
    }

    /**
     * Sync manager used during tests.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param int    $batchSize
     * @param Client $client
     *
     * @return SyncManager
     */
    private function getSyncManager(int $batchSize, Client $client): SyncManager
    {
        $connectionManager = $this->createConnectionManager($client);
        $batchDataMapperResolver = $this->getBatchDataMapperResolver();
        $batch = new Batch();
        $uuidFactory = new UuidFactory();

        return new SyncManager($batchDataMapperResolver, $connectionManager, $batch, $uuidFactory, $batchSize);
    }

    /**
     * Client Mock used during tests.
     *
     * @param int $expectedBatchCount
     *
     * @return Client
     */
    private function getClientMock(int $expectedBatchCount): Client
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->exactly($expectedBatchCount))->method('indexDocuments')->willReturnCallback(
            $this->getInsertDocsStub()
        );

        $client->method('search')->willReturnCallback($this->getSearchDocsStub());

        return $client;
    }

    /**
     * Stub function used to fake doc indexing.
     */
    private function getInsertDocsStub()
    {
        return function ($engineName, $docs) {
            $this->writtenDocs[$engineName] = array_merge($this->writtenDocs[$engineName] ?? [], $docs);
            return $docs;
        };
    }

    /**
     * Stub function for search used during wait for completion.
     */
    private function getSearchDocsStub()
    {
        return function ($engineName) {
            return ['meta' => ['page' => ['total_results' => count($this->writtenDocs[$engineName])]]];
        };
    }

    /**
     * Generate document to be indexed.
     *
     * @param $startIndex
     * @param $lastIndex
     *
     * @return Generator
     */
    private function getAddedDocuments(int $startIndex, int $lastIndex)
    {
        $docs = array_map(
            function ($docId) {
                return ['id' => $docId];
            },
            range($startIndex, $lastIndex)
        );

        foreach ($docs as $doc) {
            yield $doc;
        }
    }

    /**
     * Generate document to be deleted.
     *
     * @param $startIndex
     * @param $lastIndex
     *
     * @return Generator
     */
    private function getDeletedDocuments(int $startIndex, $lastIndex)
    {
        foreach (range($startIndex, $lastIndex) as $docId) {
            yield $docId;
        }
    }

    /**
     * Init the connection manager with a client.
     *
     * @param Client $client
     *
     * @return ConnectionManagerInterface
     */
    private function createConnectionManager(Client $client): ConnectionManagerInterface
    {
        $connectionManager = $this->createMock(ConnectionManagerInterface::class);
        $connectionManager->expects($this->once())->method('getClient')->willReturn($client);

        return $connectionManager;
    }

    /**
     * Batch data mapper used during tests.
     *
     * @return BatchDataMapperResolverInterface
     */
    private function getBatchDataMapperResolver(): BatchDataMapperResolverInterface
    {
        $batchDataMapper = $this->createMock(BatchDataMapperInterface::class);
        $batchDataMapper->method('map')->will($this->returnArgument(0));

        $resolver = $this->createMock(BatchDataMapperResolverInterface::class);
        $resolver->method('getMapper')->willReturn($batchDataMapper);

        return $resolver;
    }
}
