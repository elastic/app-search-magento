<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Test\Unit\Model\Adapter\Engine;

use Elastic\AppSearch\Model\Adapter\EngineManager;
use Elastic\AppSearch\Client\ConnectionManager;
use Elastic\AppSearch\Model\Adapter\EngineInterface;
use Swiftype\AppSearch\Client;
use Psr\Log\NullLogger;
use Swiftype\Exception\NotFoundException;
use Swiftype\Exception\ConnectionException;

/**
 * Unit test for the Elastic\AppSearch\Model\Adapter\Engine class.
 *
 * @package   Elastic\AppSearch\Test\Unit\Client
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

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $this->assertEquals(true, $engineManager->engineExists($engine));
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
        $client->expects($this->any())
               ->method('getEngine')
               ->will($this->throwException(new NotFoundException("not found")));

        $connectionManager = $this->createConnectionManager($client);

        $engineManager = new EngineManager($connectionManager, new NullLogger());

        $this->assertEquals(false, $engineManager->engineExists($engine));
    }

    /**
     * Check a LocalizedException is wrap if an exception is thrown while checking if engine exists.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    public function testEnginExistsException()
    {
        $engine = $this->createMock(EngineInterface::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->any())
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
     * Init the connection manager with a client.
     *
     * @param Client $client
     *
     * @return ConnectionManager
     */
    private function createConnectionManager($client)
    {
        $connectionManager = $this->createMock(ConnectionManager::class);
        $connectionManager->expects($this->any())->method('getClient')->willReturn($client);

        return $connectionManager;
    }
}
