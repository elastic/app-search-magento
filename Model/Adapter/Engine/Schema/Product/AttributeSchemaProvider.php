<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema\Product;


use Elastic\AppSearch\Model\Adapter\Engine\SchemaProviderInterface;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\BuilderInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider as AttributeDataProvider;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapter;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapterFactory;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;

/**
 * Add product attributes to the schema.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var AttributeDataProvider
     */
    private $attributeDataProvider;

    /**
     * @var AttributeDataProvider
     */
    private $attributeAdapterFactory;

    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

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
     * @param BuilderInterface           $builder
     * @param AttributeDataProvider      $attributeDataProvider
     * @param AttributeAdapterFactory    $attributeAdapterFactory
     * @param FieldNameResolverInterface $fieldNameResolver
     */
    public function __construct(
        BuilderInterface $builder,
        AttributeDataProvider $attributeDataProvider,
        AttributeAdapterFactory $attributeAdapterFactory,
        FieldNameResolverInterface $fieldNameResolver
    ) {
        $this->builder                 = $builder;
        $this->attributeDataProvider   = $attributeDataProvider;
        $this->attributeAdapterFactory = $attributeAdapterFactory;
        $this->fieldNameResolver       = $fieldNameResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        $productAttributes = $this->attributeDataProvider->getSearchableAttributes();

        foreach ($productAttributes as $productAttribute) {
            $attribute = $this->attributeAdapterFactory->create(['attribute' => $productAttribute]);
            foreach ($this->contexts as $contextName) {
                $fieldName = $this->fieldNameResolver->getFieldName($attribute, ['type' => $contextName]);
                $this->builder->addField($fieldName, SchemaInterface::FIELD_TYPE_TEXT);
            }
        }

        return $this->builder->build();
    }
}