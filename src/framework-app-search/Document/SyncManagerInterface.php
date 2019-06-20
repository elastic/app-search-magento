<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Document;

use Elastic\AppSearch\Framework\AppSearch\EngineInterface;

/**
 * Used to sync documents with the engine in a secured way.
 *
 * @api
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Document
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface SyncManagerInterface
{
    /**
     * Add document to be synced with the engine.
     *
     * @param EngineInterface $engine
     * @param \Traversable $documents
     */
    public function addDocuments(EngineInterface $engine, \Traversable $documents);

    /**
     * Add document to be deleted from the engine.
     *
     * @param EngineInterface $engine
     * @param \Traversable $documents
     */
    public function deleteDocuments(EngineInterface $engine, \Traversable $documents);

    /**
     * Purge documents that have been deleted using a soft delete.
     *
     * @param EngineInterface $engine
     */
    public function purgeDeletedDocuments(EngineInterface $engine);

    /**
     * Sync with the engine.
     */
    public function sync();
}
