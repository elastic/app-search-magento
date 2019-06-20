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
use Elastic\AppSearch\Framework\AppSearch\Engine\Schema\BuilderInterface as SchemaBuilderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Magento\Customer\Api\GroupRepositoryInterface as CustomerGroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Magento\Customer\Api\Data\GroupInterface as CustomerGroupInterface;

/**
 * Price fields for the product schema.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class PriceSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var string
     */
    private const PRICE_ATTRIBUTE = 'price';

    /**
     * @var SchemaBuilderInterface
     */
    private $schemaBuilder;

    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

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
     * @param SchemaBuilderInterface           $schemaBuilder
     * @param FieldMapperInterface             $fieldMapper
     * @param CustomerGroupRepositoryInterface $customerGroupRepository
     * @param SearchCriteriaBuilder            $searchCriteriaBuilder
     */
    public function __construct(
        SchemaBuilderInterface $schemaBuilder,
        FieldMapperInterface $fieldMapper,
        CustomerGroupRepositoryInterface $customerGroupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->schemaBuilder            = $schemaBuilder;
        $this->fieldMapper              = $fieldMapper;
        $this->customerGroupRepository  = $customerGroupRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        $fieldType = $this->fieldMapper->getFieldType(self::PRICE_ATTRIBUTE);

        foreach ($this->getCustomerGroups() as $customerGroup) {
            $context = ['customer_group_id' => $customerGroup->getId()];
            $fieldName = $this->fieldMapper->getFieldName(self::PRICE_ATTRIBUTE, $context);
            $this->schemaBuilder->addField($fieldName, $fieldType);
        }

        return $this->schemaBuilder->build();
    }

    /**
     * Get all available customer groups.
     *
     * @return CustomerGroupInterface[]
     */
    private function getCustomerGroups()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();

        return $this->customerGroupRepository->getList($searchCriteria)->getItems();
    }
}
