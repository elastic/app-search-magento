<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Cron;

use Elastic\AppSearch\Framework\AppSearch\Document\SyncManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineManagerInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Store\Model\StoreManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineResolverInterface;
use Magento\Store\Api\Data\StoreInterface;
use Elastic\AppSearch\CatalogSearch\Model\Config;

/**
 * Purge deleted product from the app search engine.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Cron
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class PurgeDeletedProducts
{
    /**
     * @var SyncManagerInterface
     */
    private $syncManager;

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * @var EngineManagerInterface
     */
    private $engineManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $engineIdentifier;

    /**
     * Constructor.
     *
     * @param SyncManagerInterface    $syncManager
     * @param EngineManagerInterface  $engineManager
     * @param EngineResolverInterface $engineResolver
     * @param StoreManagerInterface   $storeManager
     * @param Config                  $config
     * @param string                  $engineIdentifier
     */
    public function __construct(
        SyncManagerInterface $syncManager,
        EngineResolverInterface $engineResolver,
        EngineManagerInterface $engineManager,
        StoreManagerInterface $storeManager,
        Config $config,
        string $engineIdentifier = Fulltext::INDEXER_ID
    ) {
        $this->syncManager      = $syncManager;
        $this->engineResolver   = $engineResolver;
        $this->engineManager    = $engineManager;
        $this->storeManager     = $storeManager;
        $this->config           = $config;
        $this->engineIdentifier = $engineIdentifier;
    }

    /**
     * Loop over stores and delete products and purge all deleted products.
     */
    public function execute()
    {
        if ($this->config->isAppSearchEnabled()) {
            foreach ($this->getStores() as $store) {
                $this->purgeStoreProducts($store);
            }
        }
    }

    /**
     * Purge all deleted products for a specific store.
     *
     * @param StoreInterface $store
     */
    private function purgeStoreProducts(StoreInterface $store)
    {
        $engine = $this->engineResolver->getEngine($this->engineIdentifier, $store->getId());
        if ($this->engineManager->engineExists($engine)) {
            $this->syncManager->purgeDeletedDocuments($engine);
        }
    }

    /**
     * List of stores to be purged.
     *
     * @return StoreInterface[]
     */
    private function getStores()
    {
        return $this->storeManager->getStores(false);
    }
}
