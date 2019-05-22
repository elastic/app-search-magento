<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Synonym\Indexer;

use Magento\Framework\Indexer\DimensionProviderInterface;
use Magento\Indexer\Model\ProcessManager;
use Magento\Store\Model\StoreDimensionProvider;
use Elastic\AppSearch\Model\Synonym\Indexer\Action\Full as FullAction;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;

/**
 * Sync synonyms with the search engine.
 *
 * @package   Elastic\AppSearch\Synonym\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Synonym implements
    \Magento\Framework\Indexer\ActionInterface,
    \Magento\Framework\Mview\ActionInterface,
    \Magento\Framework\Indexer\DimensionalIndexerInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'search_synonym';

    /**
     * @var IndexerInterface
     */
    private $indexerHandler;

    /**
     * @var FullAction
     */
    private $fullAction;

    /**
     * @var DimensionProviderInterface
     */
    private $dimensionProvider;

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param FullAction                 $fullAction
     * @param IndexerHandlerFactory      $indexerHandlerFactory
     * @param DimensionProviderInterface $dimensionProvider
     * @param ProcessManager             $processManager
     */
    public function __construct(
        FullAction $fullAction,
        IndexerHandlerFactory $indexerHandlerFactory,
        DimensionProviderInterface $dimensionProvider,
        ProcessManager $processManager
    ) {
        $this->indexerHandler    = $indexerHandlerFactory->create();
        $this->fullAction        = $fullAction;
        $this->dimensionProvider = $dimensionProvider;
        $this->processManager    = $processManager;
    }

    /**
     * {@inheritDoc}
     */
    public function executeByDimensions(array $dimensions, \Traversable $entityIds = null)
    {
        if (count($dimensions) > 1 || !isset($dimensions[StoreDimensionProvider::DIMENSION_NAME])) {
            throw new \InvalidArgumentException('Indexer "' . self::INDEXER_ID . '" support only Store dimension');
        }

        $storeId  = $dimensions[StoreDimensionProvider::DIMENSION_NAME]->getValue();

        $synonyms = $this->fullAction->getSynonymSets($storeId);

        $this->indexerHandler->cleanIndex($dimensions);
        $this->indexerHandler->saveIndex($dimensions, $synonyms);
    }

    /**
     * {@inheritDoc}
     */
    public function executeFull()
    {
        if ($this->indexerHandler !== null) {
            $userFunctions = [];

            foreach ($this->dimensionProvider->getIterator() as $dimension) {
                $userFunctions[] = function () use ($dimension) {
                    $this->executeByDimensions($dimension);
                };
            }

            $this->processManager->execute($userFunctions);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     *
     * {@inheritDoc}
     */
    public function executeRow($id)
    {
        // No action on single item reindexing. Use invalidation instead.
    }

    /**
     * {@inheritDoc}
     */
    public function executeList(array $ids)
    {
        // No action on single item reindexing. Use invalidation instead.
    }

    /**
     * {@inheritDoc}
     */
    public function execute($ids)
    {
        // No action on single item reindexing. Use invalidation instead.
    }
}
