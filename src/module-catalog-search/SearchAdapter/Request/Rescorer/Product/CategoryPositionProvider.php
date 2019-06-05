<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Rescorer\Product;

use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\QueryLocatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Search\Request\IndexScopeResolverInterface as TableResolver;
use Magento\Framework\Search\Request\Dimension;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\RescorerInterface;

/**
 * List of product that are manually positionned for the current searched category.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\PositionedDocuments
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CategoryPositionProvider
{
    /**
     * @var QueryLocatorInterface
     */
    private $queryLocator;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TableResolver
     */
    private $tableResolver;

    /**
     * @var array
     */
    private $cachedPositions = [];


    /**
     * Constructor.
     *
     * @param QueryLocatorInterface  $queryLocator
     * @param ScopeResolverInterface $scopeResolver
     * @param StoreManagerInterface  $storeManager
     * @param ResourceConnection     $resource
     * @param TableResolver          $tableResolver
     */
    public function __construct(
        QueryLocatorInterface $queryLocator,
        ScopeResolverInterface $scopeResolver,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        TableResolver $tableResolver
    ) {
        $this->queryLocator  = $queryLocator;
        $this->resource      = $resource;
        $this->scopeResolver = $scopeResolver;
        $this->storeManager  = $storeManager;
        $this->tableResolver = $tableResolver;
    }

    /**
     * List manually positioned products for the current request.
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    public function getPositionedDocuments(RequestInterface $request): array
    {
        $docIds = [];

        if ($categoryId = $this->getCategoryId($request)) {
            $storeId  = $this->getStoreId($request);
            $cacheKey = $categoryId . '_' . $storeId;

            if (!isset($this->cachedPositions[$cacheKey])) {
                $this->cachedPositions[$cacheKey] = $this->getPositionedProducts($categoryId, $storeId);
            }
            $docIds = $this->cachedPositions[$cacheKey];
        }

        return $docIds;
    }

    /**
     * Return current searched category id.
     *
     * @param RequestInterface $request
     *
     * @return int|NULL
     */
    private function getCategoryId(RequestInterface $request): ?int
    {
        $categoryId = null;

        $query = $this->queryLocator->getQuery($request);

        if ($query && $query->getReference()) {
            $categoryId = (int) $query->getReference()->getValue();
        }

        return $categoryId;
    }

    /**
     * Get current search store id.
     *
     * @param RequestInterface $request
     *
     * @return int
     */
    private function getStoreId(RequestInterface $request): int
    {
        $dimension = current($request->getDimensions());
        $storeId   = $this->scopeResolver->getScope($dimension->getValue())->getId();

        if ($storeId == 0) {
            $storeId = $this->storeManager->getDefaultStoreView()->getId();
        }

        return (int) $storeId;
    }

    /**
     * List of positioned products for the current category.
     *
     * @param int $categoryId
     * @param int $storeId
     *
     * @return array
     */
    private function getPositionedProducts(int $categoryId, int $storeId): array
    {
        $select = $this->getConnection()->select()
            ->from([$this->getIndexTableName($storeId)], ['product_id'])
            ->where('store_id = ?', $storeId)
            ->where('category_id = ?', $categoryId)
            ->order('position ASC')
            ->order('product_id ASC')
            ->limit(RescorerInterface::MAX_SIZE, 0);

        return array_map('strval', $this->getConnection()->fetchCol($select));
    }

    /**
     * Return category product position table for the current store.
     *
     * @param int $storeId
     *
     * @return string
     */
    private function getIndexTableName(int $storeId): string
    {
        $dimensions = [new Dimension(\Magento\Store\Model\Store::ENTITY, $storeId)];
        $tableName  = \Magento\Catalog\Model\Indexer\Category\Product\Action\Full::MAIN_INDEX_TABLE;

        return $this->tableResolver->resolve($tableName, $dimensions);
    }

    /**
     * Returns DB connection.
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }
}
