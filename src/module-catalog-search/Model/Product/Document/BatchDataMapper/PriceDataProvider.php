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

use Elastic\AppSearch\Framework\AppSearch\Document\DataProviderInterface;
use Magento\Elasticsearch\Model\ResourceModel\Index as ResourceModel;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;

/**
 * Retrieve price data for products.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Document\BatchDataMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class PriceDataProvider implements DataProviderInterface
{
    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * Constructor.
     *
     * @param ResourceModel                $resourceModel
     * @param FieldMapperResolverInterface $fieldMapperResolver
     * @param string                       $engineIdentifier
     */
    public function __construct(
        ResourceModel $resourceModel,
        FieldMapperResolverInterface $fieldMapperResolver,
        string $engineIdentifier = Fulltext::INDEXER_ID
    ) {
        $this->resourceModel = $resourceModel;
        $this->fieldMapper   = $fieldMapperResolver->getFieldMapper($engineIdentifier);
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
            $fieldName = $this->fieldMapper->getFieldName('price', ['customer_group_id' => $customerGroupId]);
            $priceData[$fieldName] = $this->fieldMapper->mapValue('price', $price);
        }

        return $priceData;
    }
}
