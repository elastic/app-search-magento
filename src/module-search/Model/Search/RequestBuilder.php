<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Search\Model\Search;

use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Elastic\AppSearch\Framework\Search\Request\Builder;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Convert search criteria into search request.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\Search\Model\Search
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RequestBuilder
{
     /**
      * @var Builder
      */
    private $requestBuilder;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor.
     *
     * @param Builder                $requestBuilder
     * @param ScopeResolverInterface $scopeResolver
     * @param ScopeConfigInterface   $scopeConfig;
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        Builder $requestBuilder,
        ScopeResolverInterface $scopeResolver,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->scopeResolver  = $scopeResolver;
        $this->scopeConfig    = $scopeConfig;
        $this->storeManager   = $storeManager;
    }

    /**
     * Convert search criteria into a search request.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return RequestInterface
     */
    public function create(SearchCriteriaInterface $searchCriteria): RequestInterface
    {
        $this->addRequestName($searchCriteria);
        $this->addDimensions();
        $this->addPagination($searchCriteria);
        $this->addFilterGroups($searchCriteria);
        $this->addSortOrders($searchCriteria);
        $this->addPriceRangeAlgorithm();

        return $this->requestBuilder->create();
    }

    /**
     * Add filter groups to the current request.
     *
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function addFilterGroups(SearchCriteriaInterface $searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() ?? [] as $filterGroup) {
            $this->requestBuilder->addFilterGroup($filterGroup);
        }
    }

    /**
     * Bind search criteria dimension to the search request.
     */
    private function addDimensions()
    {
        $scopeId = $this->scopeResolver->getScope()->getId();

        if ($scopeId == 0) {
            $scopeId = $this->storeManager->getDefaultStoreView()->getId();
        }

        $this->requestBuilder->bindDimension('scope', $scopeId);
    }

    /**
     * Set the search request name.
     *
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function addRequestName(SearchCriteriaInterface $searchCriteria)
    {
        $this->requestBuilder->setRequestName($searchCriteria->getRequestName());
    }

    /**
     * Set request pagination params.
     *
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function addPagination(SearchCriteriaInterface $searchCriteria)
    {
        $pageSize    = (int) $searchCriteria->getPageSize();
        $currentPage = max(1, (int) $searchCriteria->getCurrentPage());

        $this->requestBuilder->setFrom(($currentPage - 1) * $pageSize);
        $this->requestBuilder->setSize($pageSize);
    }

    /**
     * Add sort orders to the request.
     *
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function addSortOrders(SearchCriteriaInterface $searchCriteria)
    {
        $this->requestBuilder->setSort($searchCriteria->getSortOrders() ?? []);
    }

    /**
     * Add the price range algorithm to the search request.
     */
    private function addPriceRangeAlgorithm()
    {
        $this->requestBuilder->bind('price_dynamic_algorithm', $this->getPriceRangeAlgorithm());
    }

    /**
     * Return currently selected price range algorithm.
     *
     * @return string
     */
    private function getPriceRangeAlgorithm()
    {
        return $this->scopeConfig->getValue(AlgorithmFactory::XML_PATH_RANGE_CALCULATION, ScopeInterface::SCOPE_STORE);
    }
}
