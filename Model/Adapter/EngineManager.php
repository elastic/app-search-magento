<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter;

use Elastic\AppSearch\Client\ConnectionManager;
use Swiftype\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Swiftype\AppSearch\Client;
use Psr\Log\LoggerInterface;

/**
 * Engine management service implementation.
 *
 * @package   Elastic\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineManager implements EngineManagerInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param ConnectionManager $connectionManager
     * @param LoggerInterface   $logger
     */
    public function __construct(ConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = $connectionManager->getClient();
    }

    /**
     * {@inheritDoc}
     */
    public function engineExists(EngineInterface $engine): bool
    {
        $hasEngine = true;

        try {
            $this->client->getEngine($engine->getName());
        } catch (NotFoundException $e) {
            $hasEngine = false;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Could not check engine exists: %1', $e->getMessage()), $e);
        }

        return $hasEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function createEngine(EngineInterface $engine): void
    {
        try {
            $this->client->createEngine($engine->getName(), $engine->getLanguage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Could not create engine: %1', $e->getMessage()), $e);
        }
    }
}
