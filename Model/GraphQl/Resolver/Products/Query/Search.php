<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\GraphQl\Resolver\Products\Query;

use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Search\Api\SearchInterface;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchResult;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchCriteria\Helper\Filter as FilterHelper;
use Magento\CatalogGraphQl\Model\Resolver\Products\Query\Filter;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchResultFactory;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Run search for the products resolver.
 *
 * @deprecated Will be removed when Magento GraphQL implementation will have better support for sort / pagination.
 *
 * @package   Elastic\AppSearch\Model\GraphQl\Resolver\Products\Query
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Search
{
    /**
     * Constructor.
     *
     * @param SearchInterface     $search
     * @param FilterHelper        $filterHelper
     * @param Filter              $filterQuery
     * @param MetadataPool        $metadataPool
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        SearchInterface $search,
        FilterHelper $filterHelper,
        Filter $filterQuery,
        MetadataPool $metadataPool,
        SearchResultFactory $searchResultFactory
    ) {
        $this->search              = $search;
        $this->filterQuery         = $filterQuery;
        $this->filterHelper        = $filterHelper;
        $this->metadataPool        = $metadataPool;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Retrieve search results for the current request.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResolveInfo $info
     *
     * @return SearchResult
     */
    public function getResult(SearchCriteriaInterface $searchCriteria, ResolveInfo $info): SearchResult
    {
        $searchResponse = $this->search->search($searchCriteria);

        $sortedProducts = [];
        $productsIds    = [];

        foreach ($searchResponse->getItems() as $item) {
            $sortedProducts[$item->getId()] = null;
            $productsIds[] = $item->getId();
        }

        $products = $this->getProducts($searchCriteria, $info, $productsIds);

        foreach ($products as $product) {
            $sortedProducts[$product[$this->getIdField()]] = $product;
        }

        $products = array_values(array_filter($sortedProducts));

        return $this->searchResultFactory->create($searchResponse->getTotalCount(), $products);
    }

    /**
     * Load products using an id filter build from the search response.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResolveInfo             $info
     * @param array                   $productIds
     *
     * @return array
     */
    private function getProducts(SearchCriteriaInterface $searchCriteria, ResolveInfo $info, array $productIds)
    {
        $searchCriteria = $this->getProductLoadSearchCriteria($searchCriteria, $productIds);

        return $this->filterQuery->getResult($searchCriteria, $info, false, true)->getProductsSearchResult();
    }

    /**
     * Build a search criteria to be used by getProducts method.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param array                   $productIds
     *
     * @return \Magento\Framework\Api\Search\SearchCriteriaInterface
     */
    private function getProductLoadSearchCriteria(SearchCriteriaInterface $searchCriteria, array $productIds)
    {
        $searchCriteria = clone($searchCriteria);

        $searchCriteria->setPageSize(0);
        $searchCriteria->setCurrentPage(1);

        $searchCriteria->setFilterGroups([]);

        $filter = $this->filterHelper->generate($this->getIdField(), 'in', $productIds);
        $this->filterHelper->add($searchCriteria, $filter);

        return $searchCriteria;
    }

    /**
     * Retrive product id field.
     *
     * @return string
     */
    private function getIdField(): string
    {
        $metadata = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);

        return $metadata->getIdentifierField();
    }
}
