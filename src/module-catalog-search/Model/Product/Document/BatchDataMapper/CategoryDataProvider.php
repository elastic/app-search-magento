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

use Magento\Elasticsearch\Model\ResourceModel\Index as ResourceModel;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolverInterface;
use Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Field\AttributeAdapterProvider as AttributeProvider;

/**
 * Retrive category data for products.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\Model\Adapter\Document\BatchDataMapper\Product
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CategoryDataProvider extends AbstractDataProvider
{
    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * Constructor.
     *
     * @param ResourceModel              $resourceModel
     * @param AttributeProvider          $attributeProvider
     * @param FieldNameResolverInterface $fieldNameResolver
     */
    public function __construct(
        ResourceModel $resourceModel,
        AttributeProvider $attributeProvider,
        FieldNameResolverInterface $fieldNameResolver
    ) {
        parent::__construct($attributeProvider, $fieldNameResolver);
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(array $entityIds, int $storeId): array
    {
        $categoryData = $this->resourceModel->getFullCategoryProductIndexData($storeId, $entityIds);

        return array_map([$this, 'processCategoryData'], $categoryData);
    }

    /**
     * Convert category data into the indexed format.
     *
     * @param array
     *
     * @return array
     */
    private function processCategoryData($data)
    {
        $categoryData = [
            $this->getFieldName('category_ids')  => array_column($data, 'id'),
            $this->getFieldName('category_name') => array_column($data, 'name'),
        ];

        return $categoryData;
    }
}
