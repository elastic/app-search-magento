<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Elastic\AppSearch\Model\Adapter\EngineManagerInterface;
use Elastic\AppSearch\Model\Adapter\EngineResolverInterface;

/**
 * Implementation of the App Search indexer handler.
 *
 * @package   Elastic\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class IndexerHandler implements IndexerInterface
{
    /**
     * @var string
     */
    const DEFAULT_ENGINE_IDENTIFIER = \Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID;

    /**
     * Default batch size
     */
    const DEFAULT_BATCH_SIZE = 100;

    /**
     * @var IndexStructureInterface
     */
    private $indexStructure;

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
     * @var Batch
     */
    private $batch;

    /**
     * @var string
     */
    private $engineIdentifier;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param IndexStructureInterface $indexStructure
     * @param EngineManagerInterface  $engineManager
     * @param EngineResolverInterface $engineResolver
     * @param ScopeResolverInterface  $scopeResolver
     * @param Batch                   $batch
     * @param string                  $engineIdentifier
     * @param int                     $batchSize
     * @param array                   $data
     */
    public function __construct(
        IndexStructureInterface $indexStructure,
        EngineManagerInterface $engineManager,
        EngineResolverInterface $engineResolver,
        ScopeResolverInterface $scopeResolver,
        Batch $batch,
        $engineIdentifier = self::DEFAULT_ENGINE_IDENTIFIER,
        $batchSize = self::DEFAULT_BATCH_SIZE,
        $data = []
    ) {
        $this->indexStructure   = $indexStructure;
        $this->engineManager    = $engineManager;
        $this->engineResolver   = $engineResolver;
        $this->scopeResolver    = $scopeResolver;
        $this->batch            = $batch;
        $this->engineIdentifier = $engineIdentifier;
        $this->batchSize        = $batchSize;
        $this->data             = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function isAvailable($dimensions = [])
    {
        $isEngineAvailable = true;

        if (!empty($dimensions)) {
            $storeId = $this->scopeResolver->getScope(current($dimensions)->getValue())->getId();
            $engine  = $this->engineResolver->getEngine($this->engineIdentifier, $storeId);

            $isEngineAvailable = $this->engineManager->engineExists($engine);
        }

        return !empty($dimensions) ? $isEngineAvailable : $this->engineManager->ping();
    }

    /**
     * {@inheritDoc}
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        // TODO: implementation.

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        // TODO: implementation.

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function cleanIndex($dimensions)
    {
        $this->indexStructure->create($this->engineIdentifier, [], $dimensions);

        return $this;
    }
}
