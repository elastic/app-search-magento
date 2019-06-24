<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Plugin\Search\Request;

use Magento\CatalogSearch\Model\Indexer\IndexStructureFactory;
use Elastic\AppSearch\CatalogSearch\Model\Config as AppSearchConfig;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Search\Request\DimensionFactory;

/**
 * Force sync of schema and search fields when the search request config is flushed.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Plugin\Search\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ConfigPlugin
{
    /**
     * @var IndexStructureInterface
     */
    private $structure;

    /**
     * @var AppSearchConfig
     */
    private $appSearchConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    /**
     * @var string
     */
    private $engineIdentifier;

    /**
     * Constructor.
     *
     * @param IndexStructureFactory $structureFactory
     * @param AppSearchConfig       $appSearchConfig
     * @param StoreManagerInterface $storeManager
     * @param string                $engineIdentifier
     */
    public function __construct(
        IndexStructureFactory $structureFactory,
        AppSearchConfig $appSearchConfig,
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory,
        string $engineIdentifier = Fulltext::INDEXER_ID
    ) {
        $this->structure        = $structureFactory->create();
        $this->appSearchConfig  = $appSearchConfig;
        $this->storeManager     = $storeManager;
        $this->dimensionFactory = $dimensionFactory;
        $this->engineIdentifier = $engineIdentifier;
    }

    /**
     * Sync engines fields on search config reset.
     */
    public function afterReset()
    {
        if ($this->appSearchConfig->isAppSearchEnabled()) {
            foreach ($this->storeManager->getStores(false) as $store) {
                $dimensions = [$this->dimensionFactory->create(['name' => 'scope', 'value' => $store->getId()])];
                $this->structure->create($this->engineIdentifier, [], $dimensions);
            }
        }
    }
}
