<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Product\Document\BatchDataMapper;

use Elastic\AppSearch\Framework\AppSearch\Document\BatchDataMapperInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider as AttributeProvider;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;


/**
 * Product attribute batch data mapper.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Document\BatchDataMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeMapper implements BatchDataMapperInterface
{
    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * @var AttributeProvider
     */
    private $attributeProvider;

    /**
     * Constructor.
     *
     * @param FieldMapperInterface $fieldMapper
     * @param AttributeProvider    $attributeProvider
     */
    public function __construct(FieldMapperInterface $fieldMapper, AttributeProvider $attributeProvider)
    {
        $this->fieldMapper       = $fieldMapper;
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function map(array $documentData, int $storeId): array
    {
        $documents = [];

        foreach ($documentData as $entityId => $entityData) {
            $documents[$entityId] = $this->createDocument($entityId);
            foreach ($entityData as $attributeId => $attributeValue) {
                $attribute = $this->getAttribute($attributeId);
                $fieldName = $this->getFieldName($attribute);
                $documents[$entityId][$fieldName] = $this->prepareValue($attribute, $attributeValue);

                $searchFieldName = $this->getFieldName($attribute, SchemaInterface::CONTEXT_SEARCH);

                if ($fieldName != $searchFieldName) {
                    $searchValue = $this->getSearchValue($attribute, $documents[$entityId][$fieldName]);
                    $documents[$entityId][$searchFieldName] = $searchValue;
                }
            }
        }

        return $documents;
    }

    /**
     * Get field name for a specific context.
     *
     * @param ProductAttributeInterface $attribute
     * @param string                    $type
     *
     * @return string
     */
    private function getFieldName(ProductAttributeInterface $attribute, string $type = SchemaInterface::CONTEXT_FILTER): string
    {
        return $this->fieldMapper->getFieldName($attribute->getAttributeCode(), ['type' => $type]);
    }

    /**
     * Create an empty product document.
     *
     * @param string $entityId
     *
     * @return array
     */
    private function createDocument($entityId)
    {
        return array_fill_keys(['id', 'entity_id'], (string) $entityId);
    }

    /**
     * Retrieve product attribute by id.
     *
     * @param int $attributeId
     *
     * @return ProductAttributeInterface
     */
    private function getAttribute($attributeId): ProductAttributeInterface
    {
        return $this->attributeProvider->getSearchableAttribute($attributeId);
    }

    /**
     * Prepare attribute value to be indexed.
     *
     * @param ProductAttributeInterface $attribute
     * @param mixed                     $value
     *
     * @return mixed
     */
    private function prepareValue(ProductAttributeInterface $attribute, $value)
    {
        $value = is_array($value) && count($value) == 1 ? current($value) : $value;

        if ($attribute->getFrontendInput() == "multiselect" && is_string($value)) {
            $value = explode(",", $value);
        }

        if (is_array($value)) {
            $value = array_values(array_unique($value));
            $isUsedForSortBy = $attribute->getUsedForSortBy();
            $isFilterable    = $attribute->getIsFilterable() || $attribute->getIsFilterableInSearch();
            if ($isUsedForSortBy && !$isFilterable) {
                $value = current($value);
            }
        }

        return $this->fieldMapper->mapValue($attribute->getAttributeCode(), $value);
    }

    /**
     * Get searchable values for attribute (e.g. convert option ids to labels).
     *
     * @param ProductAttributeInterface $attribute
     * @param mixed                     $value
     *
     * @return mixed
     */
    private function getSearchValue(ProductAttributeInterface $attribute, $value)
    {
        $attributeOptions = $attribute->getOptions();

        if (!is_array($value)) {
            $value = [$value];
        }

        if ($attributeOptions) {
            $attributeLabels = [];
            foreach ($attributeOptions as $option) {
                if (in_array($option->getValue(), $value)) {
                    $attributeLabels[] = (string) $option->getLabel();
                }
            }
            $value = $attributeLabels;
        }

        return $this->fieldMapper->mapValue($attribute->getAttributeCode(), $value);
    }
}
