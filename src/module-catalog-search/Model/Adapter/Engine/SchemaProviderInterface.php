<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine;

/**
 * Schema provider interface definition.
 *
 * @package   Elastic\Model\Adapter\Engine
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
