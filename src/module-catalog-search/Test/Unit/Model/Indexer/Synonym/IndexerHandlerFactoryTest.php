<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Indexer\Synonym;

use Elastic\AppSearch\CatalogSearch\Model\Indexer\Synonym\IndexerHandlerFactory;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\EngineResolverInterface;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\Model\Indexer\Synonym\IndexerHandlerFactory
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Indexer\Synonym
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class IndexerHandlerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test building the index save handler for the current engine.
     */
    public function testGetIndexerHandler()
    {
        $indexerHandler = $this->createMock(IndexerInterface::class);
        $indexerHandler->method('isAvailable')->willReturn(true);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('create')->willReturn($indexerHandler);

        $engineResolver = $this->createMock(EngineResolverInterface::class);
        $engineResolver->method('getCurrentSearchEngine')->willReturn('search_engine');

        $factory = $this->createFactory($objectManager, $engineResolver);
        $handler = $factory->create();

        $this->assertInstanceOf(IndexerInterface::class, $handler);
        $this->assertTrue($handler->isAvailable());
    }

    /**
     * Test building the index save handler for the current engine (not handler configured version).
     */
    public function testGetNotConfiguredIndexerHandler()
    {
        $objectManager  = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->never())->method('create');

        $engineResolver = $this->createMock(EngineResolverInterface::class);
        $engineResolver->method('getCurrentSearchEngine')->willReturn('other_engine');

        $this->assertNull($this->createFactory($objectManager, $engineResolver)->create());
    }

    /**
     * Test building the index save handler for the current engine (invalid handler configured version).
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalidIndexerHandler()
    {
        $indexerHandler = $this->createMock(self::class);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('create')->willReturn($indexerHandler);

        $engineResolver = $this->createMock(EngineResolverInterface::class);
        $engineResolver->method('getCurrentSearchEngine')->willReturn('search_engine');

        $this->createFactory($objectManager, $engineResolver)->create();
    }

    /**
     * Test building the index save handler for the current engine (not available engine version).
     *
     * @expectedException \LogicException
     */
    public function testNotAvailableIndexerHandler()
    {
        $indexerHandler = $this->createMock(IndexerInterface::class);
        $indexerHandler->method('isAvailable')->willReturn(false);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('create')->willReturn($indexerHandler);

        $engineResolver = $this->createMock(EngineResolverInterface::class);
        $engineResolver->method('getCurrentSearchEngine')->willReturn('search_engine');

        $this->createFactory($objectManager, $engineResolver)->create();
    }

    /**
     * Create the index handler factory.
     *
     * @param ObjectManagerInterface  $objectManager
     * @param EngineResolverInterface $engineResolver
     *
     * @return IndexerHandlerFactory
     */
    private function createFactory($objectManager, $engineResolver): IndexerHandlerFactory
    {
        $handlers = ['search_engine' => IndexerInterface::class];

        return new IndexerHandlerFactory($objectManager, $engineResolver, $handlers);
    }
}
