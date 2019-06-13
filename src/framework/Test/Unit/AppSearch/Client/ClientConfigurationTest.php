<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Client;

use Elastic\AppSearch\Framework\AppSearch\Client\ClientConfiguration;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Unit test for the ClientConfiguration class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ClientConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test reading API endpoint from various persisted values.
     *
     * @param mixed $endpointUrl
     *
     * @testWith ["api-endpoint"]
     *           [""]
     *           [null]
     */
    public function testGetApiEndpoint($endpointUrl)
    {
        $clientConfiguration = $this->getClientConfiguration($endpointUrl);

        $apiEndpoint = $clientConfiguration->getApiEndpoint();

        $this->assertInternalType('string', $apiEndpoint);
        $this->assertEquals($apiEndpoint, (string) $apiEndpoint);
    }

    /**
     * Test is debug mode accross various persited value.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @param mixed $debugMode
     *
     * @testWith [true]
     *           [false]
     *           [1]
     *           [0]
     *           ["1"]
     *           ["0"]
     *           [null]
     *           [""]
     */
    public function testIsDebug($debugMode = true)
    {
        $clientConfiguration = $this->getClientConfiguration($debugMode);

        $isDebug = $clientConfiguration->isDebug();

        $this->assertInternalType('bool', $isDebug);
        $this->assertEquals($isDebug, (bool) $isDebug);
    }

    /**
     * Test reading API key from various persisted values.
     *
     * @param mixed $cryptedApiKey
     *
     * @testWith [383833]
     *           ["api-key"]
     */
    public function testGetApiKey($cryptedApiKey)
    {
        foreach (['getPrivateApiKey', 'getSearchApiKey'] as $method) {
            $clientConfiguration = $this->getClientConfiguration($cryptedApiKey);

            $apiKey = $clientConfiguration->$method();

            $this->assertInternalType('string', $apiKey);
            $this->assertEquals($this->decrypt($cryptedApiKey), (string) $apiKey);
        }
    }

    /**
     * Test reading API key from various persisted values (empty use case).
     *
     * @param mixed $cryptedApiKey
     *
     * @testWith [0]
     *           [""]
     *           [null]
     */
    public function testGetEmptyApiKey($cryptedApiKey)
    {
        foreach (['getPrivateApiKey', 'getSearchApiKey'] as $method) {
            $clientConfiguration = $this->getClientConfiguration($cryptedApiKey);

            $apiKey = $clientConfiguration->$method();

            $this->assertNull($apiKey);
        }
    }

    /**
     * Test getting the integration name.
     */
    public function testGetIntegrationName()
    {
        $clientConfiguration = $this->getClientConfiguration('config');
        $this->assertEquals('magento-module:0.0.1', $clientConfiguration->getIntegrationName());
    }

    /**
     * Mock implementation the decrypt method used while reading in the config.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        return sprintf('decrypt-%s', (string) $value);
    }

    /**
     * Init a mocked client configuration.
     *
     * @param string $configValue
     *
     * @return \Elastic\AppSearch\Framework\AppSearch\Client\ClientConfiguration
     */
    private function getClientConfiguration($configValue)
    {
        $encryptor = $this->createMock(EncryptorInterface::class);
        $encryptor->expects($this->any())->method('decrypt')->will($this->returnCallback([$this, 'decrypt']));

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->any())->method('getValue')->willReturn($configValue);

        $moduleList = $this->createMock(ModuleListInterface::class);
        $moduleList->expects($this->any())->method('getOne')->willReturn(['setup_version' => '0.0.1']);

        return new ClientConfiguration($scopeConfig, $encryptor, $moduleList);
    }
}
