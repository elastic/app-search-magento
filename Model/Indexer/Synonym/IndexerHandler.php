<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Indexer\Synonym;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Elastic\AppSearch\Model\Adapter\EngineManagerInterface;
use Elastic\AppSearch\Model\Adapter\EngineResolverInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Elastic\AppSearch\Model\Adapter\EngineInterface;
use Elastic\AppSearch\Client\ConnectionManager;
use Swiftype\AppSearch\Client;

/**
 * App Search synonyms save index handler.
 *
 * @package   Elastic\AppSearch\Synonym\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class IndexerHandler implements IndexerInterface
{
    /**
     * @var EngineManagerInterface
     */
    private $engineManager;

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string[]
     */
    private $engines;

    /**
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param EngineManagerInterface  $engineManager
     * @param EngineResolverInterface $engineResolver
     * @param ScopeResolverInterface  $scopeResolver
     * @param string                  $engineIdentifier
     * @param array                   $data
     */
    public function __construct(
        EngineManagerInterface $engineManager,
        EngineResolverInterface $engineResolver,
        ScopeResolverInterface $scopeResolver,
        ConnectionManager $connectionManager,
        array $engines = [],
        array $data = []
    ) {
        $this->engineManager  = $engineManager;
        $this->engineResolver = $engineResolver;
        $this->client         = $connectionManager->getClient();
        $this->scopeResolver  = $scopeResolver;
        $this->engines        = $engines;
        $this->data           = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function isAvailable($dimensions = [])
    {
        return $this->engineManager->ping();
    }

    /**
     * {@inheritDoc}
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        foreach ($this->engines as $engineIdentifier) {
            $engine = $this->getEngine($engineIdentifier, $dimensions);
            if ($this->engineManager->engineExists($engine)) {
                foreach ($documents as $synonyms) {
                    $this->client->createSynonymSet($engine->getName(), $synonyms);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        // Does nothing since we do not support partial reindexing of synonym groups.
    }

    /**
     * {@inheritDoc}
     */
    public function cleanIndex($dimensions)
    {
        foreach ($this->engines as $engineIdentifier) {
            $engine = $this->getEngine($engineIdentifier, $dimensions);
            if ($this->engineManager->engineExists($engine)) {
                $this->purgeSynonyms($engine->getName());
            }
        }
    }

    /**
     * Purge synonyms for an engine.
     *
     * @param string $engineName
     *
     * @return void
     */
    private function purgeSynonyms(string $engineName)
    {
        do {
            $synonymSets = $this->client->listSynonymSets($engineName);
            foreach ($synonymSets['results'] as $synonymSet) {
                $this->client->deleteSynonymSet($engineName, $synonymSet['id']);
            }
        } while (!empty($synonymSets['results']));
    }

    /**
     * Get engine from identifier/store.
     *
     * @param array $dimensions
     *
     * @return EngineInterface
     */
    private function getEngine(string $engineIdentifier, array $dimensions): EngineInterface
    {
        $storeId = $this->scopeResolver->getScope(current($dimensions)->getValue())->getId();

        return $this->engineResolver->getEngine($engineIdentifier, $storeId);
    }
}
