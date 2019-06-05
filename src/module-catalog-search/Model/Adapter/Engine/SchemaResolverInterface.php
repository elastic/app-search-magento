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

use Magento\Framework\Exception\LocalizedException;

/**
 * Resolve AppSearch schema from an engine identifier.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface SchemaResolverInterface
{
    /**
     * Locate schema for an engine and return it.
     *
     * @throws LocalizedException If no schema provider exists for the engine.
     *
     * @param string $engineIdentifier
     *
     * @return SchemaInterface
     */
    public function getSchema(string $engineIdentifier): SchemaInterface;
}
