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
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;

/**
 * Retrive category data for products.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Document\BatchDataMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CategoryDataProvider implements DataProviderInterface
{
    /**
     * @var array
     */
    private $mappedField = ['id' => 'category_ids', 'name' => 'category_name'];

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
        $categoryData = [];

        foreach ($this->mappedField as $srcField => $targetField) {
            $fieldName  = $this->fieldMapper->getFieldName($targetField);
            $fieldValue = $this->fieldMapper->mapValue($targetField, array_column($data, $srcField));
            $categoryData[$fieldName] = $fieldValue;
        }

        return $categoryData;
    }
}
