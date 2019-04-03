<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter;

use Magento\Framework\Exception\LocalizedException;

/**
 * Engine management service.
 *
 * @api
 *
 * @package   Elastic\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface EngineManagerInterface
{
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
}
