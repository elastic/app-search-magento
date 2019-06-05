<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Client;

use Elastic\AppSearch\CatalogSearch\Client\ConnectionManager;
use Elastic\AppSearch\CatalogSearch\Client\ClientConfigurationInterface;
use Psr\Log\LoggerInterface;
use Swiftype\AppSearch\Client;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\Client\ConnectionManager class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ConnectionManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test getting a client without options.
     */
    public function testGetClient()
    {
        $connectionManager = new ConnectionManager($this->getClientConfig(), $this->getLogger());

        $this->assertInstanceOf(Client::class, $connectionManager->getClient());
    }

    /**
     * Test getting a client using options.
     *
     * @param array $options
     *
     * @testWith [{"api_key": "api_key"}]
     *           [{"api_endpoint": "http://hostname"}]
     *           [{"api_endpoint": "http://hostname.com"}]
     *           [{"api_endpoint": "http://hostname.com:3002"}]
     *           [{"api_endpoint": "https://hostname.com:3002"}]
     */
    public function testGetClientWithOptions($options = [])
    {
        $connectionManager = new ConnectionManager($this->getClientConfig(), $this->getLogger());

        $this->assertInstanceOf(Client::class, $connectionManager->getClient($options));
    }

    /**
     * Test an exception is thrown when using an invalid URL to configure the client.
     *
     * @todo: Use another exception type + fail on empty api_key
     *
     * @param string $apiEndpoint
     *
     * @expectedException \Swiftype\Exception\UnexpectedValueException
     */
    public function testInvalidApiEndpoint($apiEndpoint = 'not_a_valid_url')
    {
        $connectionManager = new ConnectionManager($this->getClientConfig(), $this->getLogger());
        $connectionManager->getClient(['api_endpoint' => $apiEndpoint]);
    }

    /**
     * Test an exception when the config is empty.
     *
     * @todo: Use another exception type.
     *
     * @expectedException \Swiftype\Exception\UnexpectedValueException
     */
    public function testEmptyConfiguration()
    {
        $connectionManager = new ConnectionManager($this->getClientConfig(null, null), $this->getLogger());
        $connectionManager->getClient();
    }

    /**
     * Mock class to retrive configuration.
     *
     * @param string $apiEndpoint`
     * @param string $apiKey
     *
     * @return ClientConfigurationInterface
     */
    private function getClientConfig($apiEndpoint = 'hostname', $apiKey = 'api_key')
    {
        $clientConfig = $this->createMock(ClientConfigurationInterface::class);

        $clientConfig->expects($this->any())->method('getApiEndpoint')->willReturn($apiEndpoint);
        $clientConfig->expects($this->any())->method('getPrivateApiKey')->willReturn($apiKey);
        $clientConfig->expects($this->any())->method('isDebug')->willReturn(true);

        return $clientConfig;
    }

    /**
     * Mock class for logger.
     *
     * @return LoggerInterface
     */
    private function getLogger()
    {
        return $this->createMock(LoggerInterface::class);
    }
}
