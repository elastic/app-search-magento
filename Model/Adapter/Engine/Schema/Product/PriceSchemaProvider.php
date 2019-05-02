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

use Elastic\AppSearch\Model\Adapter\Engine\Schema\BuilderInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldTypeResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapterProvider;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaProviderInterface;
use Magento\Customer\Api\GroupRepositoryInterface as CustomerGroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Price fields for the product schema.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class PriceSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var AttributeAdapterProvider
     */
    private $attributeAdapterProvider;

    /**
     * @var CustomerGroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param BuilderInterface                 $builder
     * @param AttributeAdapterProvider         $attributeAdapterProvider
     * @param FieldNameResolverInterface       $fieldNameResolver
     * @param FieldTypeResolverInterface       $fieldTypeResolver
     * @param CustomerGroupRepositoryInterface $customerGroupRepository
     * @param SearchCriteriaBuilder            $searchCriteriaBuilder
     */
    public function __construct(
        BuilderInterface $builder,
        AttributeAdapterProvider $attributeAdapterProvider,
        FieldNameResolverInterface $fieldNameResolver,
        FieldTypeResolverInterface $fieldTypeResolver,
        CustomerGroupRepositoryInterface $customerGroupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->builder                  = $builder;
        $this->attributeAdapterProvider = $attributeAdapterProvider;
        $this->fieldNameResolver        = $fieldNameResolver;
        $this->fieldTypeResolver        = $fieldTypeResolver;
        $this->customerGroupRepository  = $customerGroupRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        $priceAttribute = $this->attributeAdapterProvider->getAttributeAdapter('price');
        $fieldType      = $this->fieldTypeResolver->getFieldType($priceAttribute);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        foreach ($this->customerGroupRepository->getList($searchCriteria)->getItems() as $customerGroup) {
            $context = ['customer_group_id' => $customerGroup->getId()];
            $fieldName = $this->fieldNameResolver->getFieldName($priceAttribute, $context);
            $this->builder->addField($fieldName, $fieldType);
        }

        return $this->builder->build();
    }
}
