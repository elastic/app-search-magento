<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine;

/**
 * Schema provider interface definition.
 *
 * @api
 * @spi
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface SchemaProviderInterface
{
    /**
     * Return an AppSearch engine schema.
     *
     * @return SchemaInterface
     */
    public function getSchema(): SchemaInterface;
}
