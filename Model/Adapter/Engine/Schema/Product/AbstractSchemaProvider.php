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
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapter;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapterFactory;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;
use Magento\Eav\Api\Data\AttributeInterfaceFactory;

/**
 * Add product attributes to the schema.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
abstract class AbstractSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var AttributeDataProvider
     */
    private $attributeAdapterFactory;

    /**
     * @var AttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * Constructor.
     *
     * @param BuilderInterface           $builder
     * @param AttributeAdapterFactory    $attributeAdapterFactory
     * @param AttributeInterfaceFactory  $attributeFactory
     * @param FieldNameResolverInterface $fieldNameResolver
     */
    public function __construct(
        BuilderInterface $builder,
        AttributeAdapterFactory $attributeAdapterFactory,
        AttributeInterfaceFactory $attributeFactory,
        FieldNameResolverInterface $fieldNameResolver
    ) {
        $this->builder                 = $builder;
        $this->attributeAdapterFactory = $attributeAdapterFactory;
        $this->fieldNameResolver       = $fieldNameResolver;
        $this->attributeFactory        = $attributeFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        $attributes = $this->getAttributes();

        foreach ($attributes as $attribute) {
            $fieldName = $this->fieldNameResolver->getFieldName($attribute);
            $this->builder->addField($fieldName, SchemaInterface::FIELD_TYPE_TEXT);
        }

        return $this->builder->build();
    }

    /**
     * List of static fields definition.
     *
     * @return array
     */
    abstract protected function getAttributesData();

    /**
     * Retrieved used attributes and wrap them into an attribute adapter.
     *
     * @return AttributeAdapter[]
     */
    private function getAttributes()
    {
        $attributes = array_map(
            function ($attributeData) {
                $attribute = $this->attributeFactory->create(['data' => $attributeData]);

                return $this->attributeAdapterFactory->create(['attribute' =>  $attribute]);
            },
            $this->getAttributesData()
        );

        return $attributes;
    }
}
