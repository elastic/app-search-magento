<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Indexer\Synonym\Action;

use Magento\Search\Model\ResourceModel\SynonymGroup;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Collect synonyms for a specific store and return list of synonyms sets.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Synonym\Indexer\Action
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Full
{
    /**
     * @var SynonymGroup
     */
    private $resourceModel;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor.
     * @param SynonymGroup          $resourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(SynonymGroup $resourceModel, StoreManagerInterface $storeManager)
    {
        $this->resourceModel = $resourceModel;
        $this->storeManager  = $storeManager;
    }

    /**
     * Build a generator iterating over synonyms configured for the store.
     *
     * @param int $storeId
     *
     * @return \Generator
     */
    public function getSynonymSets(int $storeId): \Generator
    {
        $blackList   = [];

        foreach ($this->loadSynonymGroups($storeId) as $synonymsGroup) {
            $canAddGroup = true;
            $terms = explode(',', $synonymsGroup['synonyms']);
            foreach ($terms as $term) {
                $canAddGroup = $canAddGroup && !isset($blackList[$term]);
                $blackList[$term] = true;
            }

            if ($canAddGroup) {
                yield $terms;
            }
        }
    }

    /**
     * Load synonym groups filtered by store id.
     *
     * @param int $storeId
     *
     * @return array
     */
    private function loadSynonymGroups(int $storeId): array
    {
        $websiteId = $this->getWebsiteId($storeId);

        $synonymsGroups = array_merge(
            $this->resourceModel->getByScope($websiteId, $storeId),
            $this->resourceModel->getByScope($websiteId, 0),
            $this->resourceModel->getByScope(0, 0)
        );

        return $synonymsGroups;
    }

    /**
     * Get website id for a store.
     *
     * @param int $storeId
     *
     * @return int
     */
    private function getWebsiteId(int $storeId): int
    {
        return $this->storeManager->getStore($storeId)->getWebsiteId();
    }
}
