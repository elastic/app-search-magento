<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Wrap EAV attribute to be usable to build a schema.
 *
 * @package   Elastic\Model\Adapter\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeAdapter
{
    /**
     * @var CustomAttributesDataInterface
     */
    private $attribute;

    /**
     * Constructor.
     *
     * @param CustomAttributesDataInterface $attribute
     */
    public function __construct(CustomAttributesDataInterface $attribute)
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
        return (bool) ($this->getAttribute()->getIsFilterable() || $this->getAttribute()->getIsFilterableInSearch());
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
}
