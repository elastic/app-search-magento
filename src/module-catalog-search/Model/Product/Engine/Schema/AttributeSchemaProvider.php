<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Schema;

use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Schema\BuilderInterface as SchemaBuilderInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider as AttributeDataProvider;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

/**
 * Add product attributes to the schema.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var SchemaBuilderInterface
     */
    private $schemaBuilder;

    /**
     * @var AttributeDataProvider
     */
    private $attributeDataProvider;

    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * @var string[]
     */
    private $contexts = [
        SchemaInterface::CONTEXT_FILTER,
        SchemaInterface::CONTEXT_SEARCH,
        SchemaInterface::CONTEXT_SORT,
    ];

    /**
     * Constructor.
     *
     * @param SchemaBuilderInterface       $schemaBuilder
     * @param AttributeDataProvider        $attributeDataProvider
     * @param FieldMapperResolverInterface $fieldMapperResolver
     * @param string                       $engineIdentifier
     */
    public function __construct(
        SchemaBuilderInterface $schemaBuilder,
        AttributeDataProvider $attributeDataProvider,
        FieldMapperResolverInterface $fieldMapperResolver,
        string $engineIdentifier = Fulltext::INDEXER_ID
    ) {
        $this->schemaBuilder         = $schemaBuilder;
        $this->attributeDataProvider = $attributeDataProvider;
        $this->fieldMapper           = $fieldMapperResolver->getFieldMapper($engineIdentifier);
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        $productAttributes = $this->attributeDataProvider->getSearchableAttributes();

        foreach ($productAttributes as $productAttribute) {
            if ($productAttribute->getAttributeCode() != 'price') {
                $this->addAttribute($productAttribute);
            }
        }

        return $this->schemaBuilder->build();
    }

    /**
     * Add the attribute to the schema.
     *
     * @param ProductAttributeInterface $attribute
     */
    private function addAttribute(ProductAttributeInterface $attribute)
    {
        foreach ($this->contexts as $contextName) {
            $fieldName = $this->fieldMapper->getFieldName($attribute->getAttributeCode(), ['type' => $contextName]);
            $fieldType = $this->fieldMapper->getFieldType($attribute->getAttributeCode());

            $this->schemaBuilder->addField($fieldName, $fieldType);
        }
    }
}
