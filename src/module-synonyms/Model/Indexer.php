<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Synonyms\Model;

use Magento\Framework\Indexer\DimensionProviderInterface;
use Magento\Indexer\Model\ProcessManager;
use Elastic\AppSearch\Synonyms\Model\Indexer\IndexerHandlerFactory;
use Elastic\AppSearch\Synonyms\Model\Indexer\Action\Full as FullAction;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Search\Request\DimensionFactory;

/**
 * Sync synonyms with the search engine.
 *
 * @package   Elastic\AppSearch\Synonyms\Model
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Indexer implements
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

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
     * @param DimensionProviderInterface $storeManager
     * @param ProcessManager             $processManager
     */
    public function __construct(
        FullAction $fullAction,
        IndexerHandlerFactory $indexerHandlerFactory,
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory,
        ProcessManager $processManager
    ) {
        $this->indexerHandler   = $indexerHandlerFactory->create();
        $this->fullAction       = $fullAction;
        $this->storeManager     = $storeManager;
        $this->dimensionFactory = $dimensionFactory;
        $this->processManager   = $processManager;
    }

    /**
     * @SuppressWarnings(PHPMD.MissingImport)
     *
     * {@inheritDoc}
     */
    public function executeByDimensions(array $dimensions, \Traversable $entityIds = null)
    {
        if (count($dimensions) > 1 || !isset($dimensions['scope'])) {
            throw new \InvalidArgumentException('Indexer "' . self::INDEXER_ID . '" support only Store dimension');
        }

        $storeId  = $dimensions['scope']->getValue();

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

            $storeIds = array_keys($this->storeManager->getStores());

            foreach ($storeIds as $storeId) {
                $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
                $userFunctions[] = function () use ($dimension) {
                    $this->executeByDimensions([$dimension->getName() => $dimension]);
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
