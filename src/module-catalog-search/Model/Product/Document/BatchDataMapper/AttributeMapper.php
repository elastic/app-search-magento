<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Product\Document\BatchDataMapper\Product;

use Elastic\AppSearch\Framework\AppSearch\Document\BatchDataMapperInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolverInterface;
use Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Field\AttributeAdapterProvider as AttributeProvider;
use Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Field\AttributeAdapter;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;


/**
 * Product attribute batch data mapper.
 *
 * @package   Elastic\Model\Adapter\Document\BatchDataMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeMapper implements BatchDataMapperInterface
{
    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var AttributeProvider
     */
    private $attributeProvider;

    /**
     * Constructor.
     *
     * @param FieldNameResolverInterface $fieldNameResolver
     * @param AttributeProvider          $attributeProvider
     */
    public function __construct(FieldNameResolverInterface $fieldNameResolver, AttributeProvider $attributeProvider)
    {
        $this->fieldNameResolver = $fieldNameResolver;
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

    private function getFieldName(AttributeAdapter $attribute, $type = SchemaInterface::CONTEXT_FILTER)
    {
        return $this->fieldNameResolver->getFieldName($attribute, ['type' => $type]);
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
        return array_fill_keys(['id', 'entity_id'], $entityId);
    }

    /**
     * Retrieve product attribute by id.
     *
     * @param int $attributeId
     *
     * @return AttributeAdapter
     */
    private function getAttribute($attributeId)
    {
        return $this->attributeProvider->getAttributeAdapter($attributeId);
    }

    /**
     * Prepare attribute value to be indexed.
     *
     * @param AttributeAdapter $attribute
     * @param mixed            $value
     *
     * @return mixed
     */
    private function prepareValue(AttributeAdapter $attribute, $value)
    {
        $value = is_array($value) && count($value) == 1 ? current($value) : $value;

        if ($attribute->getFrontendInput() == "multiselect" && is_string($value)) {
            $value = explode(",", $value);
        }

        if (is_array($value)) {
            $value = array_values(array_unique($value));
            if ($attribute->isSortable() && !$attribute->isFilterable()) {
                $value = current($value);
            }
        }

        return $value;
    }

    /**
     * Get searchable values for attribute (e.g. convert option ids to labels).
     *
     * @param AttributeAdapter $attribute
     * @param mixed          $value
     *
     * @return mixed
     */
    private function getSearchValue(AttributeAdapter $attribute, $value)
    {
        $attributeOptions = $attribute->getOptions();

        if (!is_array($value)) {
            $value = [$value];
        }

        if ($attributeOptions) {
            $attributeLabels = [];
            foreach ($attributeOptions as $option) {
                if (\in_array($option->getValue(), $value)) {
                    $attributeLabels[] = (string) $option->getLabel();
                }
            }
            $value = $attributeLabels;
        }

        return $value;
    }
}
