<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Elastic\AppSearch\Framework\AppSearch\Document\SyncManagerInterface;

/**
 * Implementation of the App Search indexer handler.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class IndexerHandler implements IndexerInterface
{
    /**
     * @var IndexStructureInterface
     */
    private $indexStructure;

    /**
     * @var EngineManagerInterface
     */
    private $engineManager;

    /**
     * @var SyncManagerInterface
     */
    private $syncManager;

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var string
     */
    private $engineIdentifier;

    /**
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param IndexStructureInterface $indexStructure
     * @param EngineManagerInterface  $engineManager
     * @param SyncManagerInterface    $syncManager
     * @param EngineResolverInterface $engineResolver
     * @param ScopeResolverInterface  $scopeResolver
     * @param string                  $engineIdentifier
     * @param array                   $data
     */
    public function __construct(
        IndexStructureInterface $indexStructure,
        EngineManagerInterface $engineManager,
        SyncManagerInterface $syncManager,
        EngineResolverInterface $engineResolver,
        ScopeResolverInterface $scopeResolver,
        string $engineIdentifier,
        $data = []
    ) {
        $this->indexStructure   = $indexStructure;
        $this->engineManager    = $engineManager;
        $this->syncManager      = $syncManager;
        $this->engineResolver   = $engineResolver;
        $this->scopeResolver    = $scopeResolver;
        $this->engineIdentifier = $engineIdentifier;
        $this->data             = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function isAvailable($dimensions = [])
    {
        $isEngineAvailable = true;

        if (!empty($dimensions)) {
            $engine = $this->getEngine($dimensions);
            $isEngineAvailable = $this->engineManager->engineExists($engine);
        }

        return !empty($dimensions) ? $isEngineAvailable : $this->engineManager->ping();
    }

    /**
     * {@inheritDoc}
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        $engine = $this->getEngine($dimensions);
        $this->syncManager->addDocuments($engine, $documents);
        $this->syncManager->sync();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $engine = $this->getEngine($dimensions);
        $this->syncManager->deleteDocuments($engine, $documents);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function cleanIndex($dimensions)
    {
        $this->indexStructure->create($this->engineIdentifier, [], $dimensions);
        $this->syncManager->deleteAllDocuments($this->getEngine($dimensions));

        return $this;
    }

    /**
     * Get engine from dimensions.
     *
     * @param array $dimensions
     *
     * @return EngineInterface
     */
    private function getEngine($dimensions): EngineInterface
    {
        $storeId = $this->scopeResolver->getScope(current($dimensions)->getValue())->getId();

        return $this->engineResolver->getEngine($this->engineIdentifier, $storeId);
    }
}
