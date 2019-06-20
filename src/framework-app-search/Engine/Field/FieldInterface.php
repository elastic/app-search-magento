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
 * Engine field interface.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldInterface
{
    /**
     * Field name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Field type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Check if field if filterable.
     *
     * @return bool
     */
    public function isFilterable(): bool;

    /**
     * Check if field is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool;

    /**
     * Check if field is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool;

    /**
     * Check if the field use an additional field to store search value.
     *
     * @return bool
     */
    public function useValueField(): bool;
}
