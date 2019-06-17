<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Client;

use Psr\Log\LoggerInterface;
use Elastic\AppSearch\Client\ClientBuilder;
use Elastic\AppSearch\Client\Client;

/**
 * Implementation of the App Search connection manager.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ConnectionManager implements ConnectionManagerInterface
{
    /**
     * @var \Elastic\AppSearch\Client\Client
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
        $this->logger        = $logger;
        $this->clientConfig  = $clientConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient(array $options = []): Client
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
     * @return \Elastic\AppSearch\Client\Client
     */
    private function createClient($options = [])
    {
        $apiEndpoint = $options['api_endpoint'] ?? $this->clientConfig->getApiEndpoint();
        $apiKey = $options['api_key'] ?? $this->clientConfig->getPrivateApiKey();

        $clientBuilder = ClientBuilder::create($apiEndpoint, $apiKey);

        $clientBuilder->setLogger($this->logger);
        $clientBuilder->setIntegration($this->clientConfig->getIntegrationName());

        if ($this->clientConfig->isDebug()) {
            $clientBuilder->setTracer($this->logger);
        }

        return $clientBuilder->build();
    }
}
