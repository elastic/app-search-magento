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

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider as AttributeDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

/**
 * Retrieve engine fields from product EAV attributes.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeFieldProvider implements FieldProviderInterface
{
    /**
     * @var FieldInterface[]
     */
    private $fields;

    /**
     * @var AttributeDataProvider
     */
    private $attributeDataProvider;

    /**
     * @var AttributeFieldFactory
     */
    private $fieldFactory;

    /**
     * Constructor.
     *
     * @param AttributeDataProvider $attributeDataProvider
     * @param AttributeFieldFactory $fieldFactory
     */
    public function __construct(AttributeDataProvider $attributeDataProvider, AttributeFieldFactory $fieldFactory)
    {
        $this->attributeDataProvider = $attributeDataProvider;
        $this->fieldFactory          = $fieldFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields(): array
    {
        if ($this->fields === null) {
            foreach ($this->attributeDataProvider->getSearchableAttributes() as $attribute) {
                $field = $this->createField($attribute);
                $this->fields[$field->getName()] = $field;
            }
        }

        return $this->fields;
    }

    /**
     * {@inheritDoc}
     */
    public function getField(string $name): FieldInterface
    {
        $fields = $this->getFields();

        if (!isset($fields[$name])) {
            throw new LocalizedException(__('Unable to find field %1 in attribute list.', $name));
        }

        return $fields[$name];
    }

    /**
     * Create an engine field from the product attribute.
     *
     * @param ProductAttributeInterface $attribute
     *
     * @return FieldInterface
     */
    private function createField(ProductAttributeInterface $attribute): FieldInterface
    {
        return $this->fieldFactory->create(['attribute' => $attribute]);
    }
}
