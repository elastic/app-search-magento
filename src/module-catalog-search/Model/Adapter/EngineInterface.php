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
 * App Search Engine interface.
 *
 * @api
 *
 * @package   Elastic\Model\Adapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface EngineInterface
{
    /**
     * Identifier of the engine used in Magento (e.g. catalogsearch_fulltext).
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Store id associated with the current engine.
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * Real engine name (e.g. magento2-catalogsearch-fulltext-1).
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Language code of the current engine (null if universal).
     *
     * @return string|NULL
     */
    public function getLanguage(): ?string;
}
