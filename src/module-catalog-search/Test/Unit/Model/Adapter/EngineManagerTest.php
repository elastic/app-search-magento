<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Adapter;

use Elastic\AppSearch\CatalogSearch\Model\Adapter\EngineManager;
use Elastic\AppSearch\Framework\AppSearch\Client\ConnectionManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Swiftype\AppSearch\Client;
use Psr\Log\NullLogger;
use Swiftype\Exception\NotFoundException;
use Swiftype\Exception\ConnectionException;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\Model\Adapter\EngineManager class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test engine exists method return true when no exception is thrown by the client.
     *
     * @return void
     */
    public function testEngineExists()
    {
        $engine = $this->createMock(EngineInterface::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())->method('getEngine')->willReturn([]);

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $this->assertEquals(true, $engineManager->engineExists($engine));

        // Check the result is cached.
        $engineManager->engineExists($engine);
    }

    /**
     * Test engine exists method return true when a NotFoundException exception is thrown by the client.
     *
     * @return void
     */
    public function testEngineDoesNotExists()
    {
        $engine = $this->createMock(EngineInterface::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
               ->method('getEngine')
               ->will($this->throwException(new NotFoundException("not found")));

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $this->assertEquals(false, $engineManager->engineExists($engine));

        // Check the result is cached.
        $engineManager->engineExists($engine);
    }

    /**
     * Check a LocalizedException is wrap if an exception is thrown while checking if engine exists.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    public function testEngineExistsException()
    {
        $engine = $this->createMock(EngineInterface::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
               ->method('getEngine')
               ->will($this->throwException(new ConnectionException("message")));

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $engineManager->engineExists($engine);
    }

    /**
     * Check engine creation can be run.
     */
    public function testCreateEngine()
    {
        $engines = [];

        $engine = $this->createMock(EngineInterface::class);

        $addEngine = function ($engine) use (&$engines) {
            $engines[] = $engine;
        };

        $client = $this->createMock(Client::class);
        $client->expects($this->any())
               ->method('createEngine')
               ->willReturnCallback($addEngine);

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $engineManager->createEngine($engine);

        $this->assertCount(1, $engines);
    }

    /**
     * Check a LocalizedException is wrap if an exception is thrown while checking if engine exists.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    public function testCreateEngineException()
    {
        $engine = $this->createMock(EngineInterface::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->any())
            ->method('createEngine')
            ->will($this->throwException(new ConnectionException("message")));

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $engineManager->createEngine($engine);
    }

    /**
     * Test the ping method of the engine manager.
     *
     * @return void
     */
    public function testPing()
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())->method('listEngines')->willReturn([]);

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $isAvailable = $engineManager->ping();

        $this->assertInternalType('bool', $isAvailable);
        $this->assertEquals(true, $isAvailable);

        // Check the result is cached.
        $engineManager->ping();
    }

    /**
     * Test the ping method of the engine manager when an exception occurs.
     *
     * @return void
     */
    public function testPingError()
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('listEngines')
            ->will($this->throwException(new ConnectionException("message")));

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $isAvailable = $engineManager->ping();

        $this->assertInternalType('bool', $isAvailable);
        $this->assertEquals(false, $isAvailable);

        // Check the result is cached.
        $engineManager->ping();
    }

    /**
     * Init the connection manager with a client.
     *
     * @param Client $client
     *
     * @return ConnectionManagerInterface
     */
    private function createConnectionManager($client)
    {
        $connectionManager = $this->createMock(ConnectionManagerInterface::class);
        $connectionManager->expects($this->any())->method('getClient')->willReturn($client);

        return $connectionManager;
    }
}
