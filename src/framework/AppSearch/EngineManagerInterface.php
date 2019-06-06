<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch;

use Magento\Framework\Exception\LocalizedException;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;

/**
 * Engine management service.
 *
 * @api
 *
 * @package   Elastic\AppSearch\Framework\AppSearch
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface EngineManagerInterface
{
    /**
     * Test if we can connect to App Search.
     *
     * @return bool
     */
    public function ping(): bool;

    /**
     * Check an engine exists.
     *
     * @throws LocalizedException
     *
     * @param EngineInterface $engine
     *
     * @return bool
     */
    public function engineExists(EngineInterface $engine): bool;

    /**
     * Create an engine.
     *
     * @throws LocalizedException
     *
     * @param EngineInterface $engine
     *
     * @return void
     */
    public function createEngine(EngineInterface $engine): void;

    /**
     * Update an engine schema.
     *
     * @throws LocalizedException
     *
     * @param EngineInterface $engine
     * @param SchemaInterface $schema
     *
     * @return void
     */
    public function updateSchema(EngineInterface $engine, SchemaInterface $schema): void;
}
