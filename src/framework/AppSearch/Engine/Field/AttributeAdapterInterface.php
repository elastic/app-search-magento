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
 * Wrap an attribute to be usable to build a field.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface AttributeAdapterInterface
{
    /**
     * Attribute code.
     *
     * @return string|NULL
     */
    public function getAttributeCode(): ?string;

    /**
     * Attribute frontend type.
     *
     * @return string|NULL
     */
    public function getFrontendInput(): ?string;

    /**
     * Check if attribute if filterable.
     *
     * @return bool
     */
    public function isFilterable(): bool;

    /**
     * Check if attribute is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool;

    /**
     * Check if attribute is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool;

    /**
     * Check if an attribute is a number.
     *
     * @return boolean
     */
    public function isNumberType(): bool;

    /**
     * Check if an attribute is a date.
     *
     * @return boolean
     */
    public function isDateType(): bool;

    /**
     * Return attribute options.
     *
     * @return array
     */
    public function getOptions(): ?array;
}
