<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Document\BatchDataMapper\Product;

use Magento\Elasticsearch\Model\ResourceModel\Index as ResourceModel;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolverInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\AttributeAdapterProvider as AttributeProvider;

/**
 * Retrieve price data for products.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\Model\Adapter\Document\BatchDataMapper\Product
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class PriceDataProvider extends AbstractDataProvider
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
        $priceData = $this->resourceModel->getPriceIndexData($entityIds, $storeId);

        return array_map([$this, 'processData'], $priceData);
    }

    /**
     * Convert price data into the index format.
     *
     * @param array $data
     *
     * @return array
     */
    private function processData($data)
    {
        $priceData = [];

        foreach ($data as $customerGroupId => $price) {
            $priceData[$this->getFieldName('price', ['customer_group_id' => $customerGroupId])] = $price;
        }

        return $priceData;
    }
}
