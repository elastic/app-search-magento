<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Field;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\AttributeAdapterInterface;
use Magento\Eav\Api\Data\AttributeInterface;

/**
 * Wrap EAV attribute to be usable to build a schema.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeAdapter implements AttributeAdapterInterface
{
    /**
     * @var AttributeInterface
     */
    private $attribute;

    /**
     * Constructor.
     *
     * @param AttributeInterface $attribute
     */
    public function __construct(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Attribute code.
     *
     * @return string|NULL
     */
    public function getAttributeCode(): ?string
    {
        return $this->attribute->getAttributeCode();
    }

    /**
     * Attribute frontend type.
     *
     * @return string|NULL
     */
    public function getFrontendInput(): ?string
    {
        return $this->attribute->getFrontendInput();
    }

    /**
     * Check if attribute if filterable.
     *
     * @return bool
     */
    public function isFilterable(): bool
    {
        return (bool) ($this->attribute->getIsFilterable() || $this->attribute->getIsFilterableInSearch());
    }

    /**
     * Check if attribute is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return (bool) ($this->attribute->getIsSearchable() || $this->attribute->getIsVisibleInAdvancedSearch());
    }

    /**
     * Check if attribute is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool
    {
        return (bool) $this->attribute->getUsedForSortBy();
    }

    /**
     * Check if an attribute is a number.
     *
     * @return boolean
     */
    public function isNumberType(): bool
    {
        $isNumber = false;
        $backendType = $this->attribute->getBackendType();

        if (in_array($backendType, ['decimal', 'int', 'smallint'])) {
            $frontendType = $this->getFrontendInput();
            $isNumber = !in_array($frontendType, ['select', 'multiselect', 'boolean']);
        }

        return $isNumber;
    }

    /**
     * Check if an attribute is a date.
     *
     * @return boolean
     */
    public function isDateType(): bool
    {
        return in_array($this->attribute->getBackendType(), ['timestamp', 'datetime']);
    }

    /**
     * Return attribute options.
     *
     * @return array
     */
    public function getOptions(): ?array
    {
        return $this->attribute->getOptions();
    }
}
