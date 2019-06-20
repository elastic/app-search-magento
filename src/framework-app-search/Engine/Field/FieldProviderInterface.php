<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field;

/**
 * Retrieve engine fields.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldProviderInterface
{
    /**
     * Get field by name.
     *
     * @param string $name
     *
     * @return FieldInterface
     */
    public function getField(string $name): FieldInterface;

    /**
     * Get all fields.
     *
     * @return FieldInterface[]
     */
    public function getFields(): array;
}
