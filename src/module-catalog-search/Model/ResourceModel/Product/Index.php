<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Indexer\WebsiteDimensionProvider;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Search\Request\IndexScopeResolverInterface;
use Magento\Catalog\Model\Indexer\Product\Price\DimensionCollectionFactory;
use Magento\Framework\Search\Request\Dimension;
use Magento\Catalog\Model\Indexer\Category\Product\AbstractAction;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * AppSearch search product index resource model.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\ResourceModel\Product
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Index extends AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var IndexScopeResolverInterface
     */
    private $tableResolver;

    /**
     * @var DimensionCollectionFactory|null
     */
    private $dimensionCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * Constructor.
     *
     * @param Context                     $context
     * @param StoreManagerInterface       $storeManager
     * @param DimensionCollectionFactory  $dimensionCollectionFactory
     * @param IndexScopeResolverInterface $tableResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param string                      $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DimensionCollectionFactory $dimensionCollectionFactory,
        IndexScopeResolverInterface $tableResolver,
        CategoryRepositoryInterface $categoryRepository,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->storeManager               = $storeManager;
        $this->tableResolver              = $tableResolver;
        $this->dimensionCollectionFactory = $dimensionCollectionFactory;
        $this->categoryRepository         = $categoryRepository;
    }

    /**
     * Load product price data to be indexed from the index.
     *
     * @param array $productIds
     * @param int   $storeId
     *
     * @return array
     */
    public function getPriceIndexData(array $productIds, int $storeId)
    {
        $websiteId = (int) $this->storeManager->getStore($storeId)->getWebsiteId();

        return $this->getProductPriceData($productIds, $websiteId);
    }

    /**
     * Load category product data to be indexed from the index.
     *
     * @param array $productIds
     * @param int   $storeId
     *
     * @return array
     */
    public function getCategoryProductIndexData(array $productIds, int $storeId)
    {
        $categoryData = [];
        $select = $this->getCategoryProductSelect($productIds, $storeId);
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $categoryData[$row['product_id']]['category_ids'][] = $row['category_id'];
            $category = $this->categoryRepository->get($row['category_id'], $storeId);
            if ($category->getLevel() > 1) {
                $categoryData[$row['product_id']]['category_name'][] = $category->getName();
            }
        }

        return $categoryData;
    }

    // phpcs:disable
    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritDoc}
     */
    protected function _construct()
    {
    }
    // phpcs:enable

    /**
     * Load price data.
     *
     * @param array $productIds
     * @param int   $websiteId
     *
     * @return array
     */
    private function getProductPriceData(array $productIds, int $websiteId)
    {
        $result = [];
        $select = $this->getProductPriceSelect($productIds, $websiteId);

        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $result[$row['entity_id']][$row['customer_group_id']] = round($row['min_price'], 2);
        }

        return $result;
    }

    /**
     * Prepare SQL Select used to load the price data.
     *
     * @param array $productIds
     * @param int   $websiteId
     *
     * @return \Magento\Framework\DB\Select
     */
    private function getProductPriceSelect(array $productIds, int $websiteId)
    {
        $selects       = [];
        $connection    = $this->getConnection();
        $dimensionName = WebsiteDimensionProvider::DIMENSION_NAME;
        $fields        = ['entity_id', 'customer_group_id', 'min_price'];

        foreach ($this->dimensionCollectionFactory->create() as $dimensions) {
            if (!isset($dimensions[$dimensionName]) || $dimensions[$dimensionName]->getValue() === $websiteId) {
                $tableName = $this->tableResolver->resolve('catalog_product_index_price', $dimensions);
                $select = $connection->select()->from($tableName, $fields)->where('website_id = ?', $websiteId);
                if ($productIds) {
                    $select->where('entity_id IN (?)', $productIds);
                }
                $selects[] = $select;
            }
        }

        return $connection->select()->union($selects);
    }

    /**
     * Prepare SQL Select used to load category product data.
     *
     * @param array $productIds
     * @param int   $storeId
     *
     * @return \Magento\Framework\DB\Select
     */
    private function getCategoryProductSelect(array $productIds, int $storeId)
    {
        $connection = $this->getConnection();
        $dimensions = [new Dimension(\Magento\Store\Model\Store::ENTITY, $storeId)];
        $tableName  = $this->tableResolver->resolve(AbstractAction::MAIN_INDEX_TABLE, $dimensions);
        $fields     = ['category_id', 'product_id'];

        $select = $connection->select()->from([$tableName], $fields)->where('store_id = ?', $storeId);

        if ($productIds) {
            $select->where('product_id IN (?)', $productIds);
        }

        return $select;
    }
}
