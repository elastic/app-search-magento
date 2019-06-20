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

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;

/**
 * A field interface implementation used to represent product attributes.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeField implements FieldInterface
{
    /**
     * @var ProductAttributeInterface
     */
    private $attribute;

    /**
     * Constructor.
     *
     * @param ProductAttributeInterface $attribute
     */
    public function __construct(ProductAttributeInterface $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->attribute->getAttributeCode();
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        $fieldType = SchemaInterface::FIELD_TYPE_TEXT;

        if ($this->isNumberType()) {
            $fieldType = SchemaInterface::FIELD_TYPE_NUMBER;
        } elseif ($this->isDateType()) {
            $fieldType = SchemaInterface::FIELD_TYPE_DATE;
        }

        return $fieldType;
    }

    /**
     * {@inheritDoc}
     */
    public function isSearchable(): bool
    {
        return (bool) ($this->attribute->getIsSearchable() || $this->attribute->getIsVisibleInAdvancedSearch());
    }

    /**
     * {@inheritDoc}
     */
    public function isFilterable(): bool
    {
        return (bool) ($this->attribute->getIsFilterable() || $this->attribute->getIsFilterableInSearch());
    }

    /**
     * {@inheritDoc}
     */
    public function isSortable(): bool
    {
        return (bool) $this->attribute->getUsedForSortBy();
    }

    /**
     * {@inheritDoc}
     */
    public function useValueField(): bool
    {
        $useValueField = in_array($this->attribute->getFrontendInput(), ['select', 'multiselect', 'boolean']);

        return $useValueField && ($this->isSearchable() || $this->isSortable());
    }

    /**
     * Check if an attribute is a number.
     *
     * @return boolean
     */
    private function isNumberType(): bool
    {
        $isNumber    = false;
        $backendType = $this->attribute->getBackendType();

        if (in_array($backendType, ['decimal', 'int', 'smallint'])) {
            $frontendType = $this->attribute->getFrontendInput();
            $isNumber = !in_array($frontendType, ['select', 'multiselect', 'boolean']);
        }

        return $isNumber;
    }

    /**
     * Check if an attribute is a date.
     *
     * @return boolean
     */
    private function isDateType(): bool
    {
        return in_array($this->attribute->getBackendType(), ['timestamp', 'datetime']);
    }
}
