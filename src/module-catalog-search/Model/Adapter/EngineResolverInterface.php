<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter;

/**
 * Resolve App Search Engine from config.
 *
 * @api
 *
 * @package   Elastic\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface EngineResolverInterface
{
    /**
     * Locate the engine and return it.
     *
     * @param string $engineIdentifier Engine identifier.
     * @param int    $storeId          Store id.
     *
     * @return EngineInterface
     */
    public function getEngine(string $engineIdentifier, int $storeId): EngineInterface;
}
