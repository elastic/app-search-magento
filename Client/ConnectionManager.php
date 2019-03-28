<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client;

use Psr\Log\LoggerInterface;
use Swiftype\AppSearch\ClientBuilder;

/**
 * Retrieve a configured and ready to go App Search client.
 *
 * @api
 *
 * @package   Elastic\AppSearch\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ConnectionManager
{
    /**
     * @var \Swiftype\AppSearch\Client
     */
    private $client;

    /**
     * @var ClientConfigurationInterface
     */
    private $clientConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param ClientConfigurationInterface $clientConfig
     * @param LoggerInterface              $logger
     */
    public function __construct(ClientConfigurationInterface $clientConfig, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->clientConfig = $clientConfig;
    }

    /**
     * Retrieve the configured App Search client.
     *
     * @param array $options
     *
     * @return \Swiftype\AppSearch\Client
     */
    public function getClient($options = [])
    {
        if (null === $this->client) {
            $this->client = $this->createClient($options);
        }

        return $this->client;
    }

    /**
     * Create the App Search client.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param array $options
     *
     * @return \Swiftype\AppSearch\Client
     */
    private function createClient($options = [])
    {
        $apiEndpoint = $options['api_endpoint'] ?? $this->clientConfig->getApiEndpoint();
        $apiKey = $options['api_key'] ?? $this->clientConfig->getApiKey();

        $clientBuilder = ClientBuilder::create($apiEndpoint, $apiKey);

        $clientBuilder->setLogger($this->logger);

        if ($this->clientConfig->isDebug()) {
            $clientBuilder->setTracer($this->logger);
        }

        return $clientBuilder->build();
    }
}
