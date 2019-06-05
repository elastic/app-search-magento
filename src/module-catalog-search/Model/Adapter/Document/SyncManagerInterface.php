<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Document;

use Elastic\AppSearch\CatalogSearch\Model\Adapter\EngineInterface;

/**
 * Used to sync documents with the engine in a secured way.
 *
 * @api
 *
 * @package   Elastic\Model\Adapter\Document
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
     * Sync with the engine.
     */
    public function sync();
}
