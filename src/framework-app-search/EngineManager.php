<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch;

use Elastic\AppSearch\Framework\AppSearch\Client\ConnectionManagerInterface;
use Elastic\OpenApi\Codegen\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Elastic\AppSearch\Client\Client;
use Psr\Log\LoggerInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;

/**
 * Engine management service implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch
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
     * @var bool
     */
    private $ping;

    /**
     * @var bool[]
     */
    private $engines = [];

    /**
     * Constructor.
     *
     * @param ConnectionManagerInterface $connectionManager
     * @param LoggerInterface            $logger
     */
    public function __construct(ConnectionManagerInterface $connectionManager, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = $connectionManager->getClient();
    }

    /**
     * {@inheritDoc}
     */
    public function ping(): bool
    {
        if (!isset($this->ping)) {
            try {
                $this->client->listEngines();
                $this->ping = true;
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->ping = false;
            }
        }

        return $this->ping;
    }

    /**
     * {@inheritDoc}
     */
    public function engineExists(EngineInterface $engine): bool
    {
        if (!isset($this->engines[$engine->getName()])) {
            try {
                $this->client->getEngine($engine->getName());
                $this->engines[$engine->getName()] = true;
            } catch (NotFoundException $e) {
                // Does nothing. Yhe engine does not exists. Just return false.
            } catch (\Exception $e) {
                $this->engines[$engine->getName()] = false;
                $this->logger->critical($e);
                throw new LocalizedException(__('Could not check engine exists: %1', $e->getMessage()), $e);
            }
        }

        return $this->engines[$engine->getName()] ?? false;
    }

    /**
     * {@inheritDoc}
     */
    public function createEngine(EngineInterface $engine): void
    {
        try {
            $this->client->createEngine($engine->getName(), $engine->getLanguage());
            while ($this->engineExists($engine) === false) {
                usleep(100);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Could not create engine: %1', $e->getMessage()), $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updateSchema(EngineInterface $engine, SchemaInterface $schema): void
    {
        try {
            $this->client->updateSchema($engine->getName(), $schema->getFields());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Could not update engine schema: %1', $e->getMessage()), $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updateSearchFields(EngineInterface $engine, array $searchFields): void
    {
        try {
            $searchSettings = $this->client->getSearchSettings($engine->getName());
            $searchSettings['search_fields'] = $searchFields;
            $this->client->updateSearchSettings($engine->getName(), $searchSettings);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Could not update engine search settings: %1', $e->getMessage()), $e);
        }
    }
}
