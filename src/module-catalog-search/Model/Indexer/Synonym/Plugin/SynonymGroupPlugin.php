<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Indexer\Synonym\Plugin;

use Magento\Search\Model\ResourceModel\SynonymGroup as SynonymGroupResourceModel;
use Magento\Framework\Indexer\IndexerRegistry;
use Elastic\AppSearch\CatalogSearch\Model\Indexer\Synonym\Indexer;

/**
 * Invalidate the index when a change occurs in synonyms groups.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Synonym\Indexer\Plugin
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SynonymGroupPlugin
{
    private $indexerRegistry;

    /**
     * Constructor.
     *
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(IndexerRegistry $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Invalidate the index after synonym group have been saved.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param SynonymGroupResourceModel $resourceModel
     * @param SynonymGroupResourceModel $result
     *
     * @return SynonymGroupResourceModel
     */
    public function afterSave(SynonymGroupResourceModel $resourceModel, $result)
    {
        $this->invalidateIndex();

        return $result;
    }


    /**
     * Invalidate the index after synonym group deletion.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param SynonymGroupResourceModel $resourceModel
     * @param SynonymGroupResourceModel $result
     *
     * @return SynonymGroupResourceModel
     */
    public function afterDelete(SynonymGroupResourceModel $resourceModel, $result)
    {
        $this->invalidateIndex();

        return $result;
    }

    /**
     * Invalidate the synonym indexer.
     *
     * @return void
     */
    private function invalidateIndex()
    {
        $indexer = $this->indexerRegistry->get(Indexer::INDEXER_ID);
        $indexer->invalidate();
    }
}
