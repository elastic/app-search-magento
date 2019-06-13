<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\GraphQl\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Elastic\AppSearch\CatalogSearch\Model\GraphQl\Resolver\Products\Query\Search as SearchQuery;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\SearchFilter;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * AppSearch search GraphQL products resolver.
 *
 * @deprecated Will be removed when Magento GraphQL implementation will have better support for sort / pagination.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\GraphQl\Resolver
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Products implements ResolverInterface
{
    /**
     * @var SearchQuery
     */
    private $searchQuery;

    /**
     * @var SearchFilter
     */
    private $searchFilter;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param SearchQuery           $searchQuery
     * @param SearchFilter          $searchFilter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LayerResolver         $layerResolver
     */
    public function __construct(
        SearchQuery $searchQuery,
        SearchFilter $searchFilter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LayerResolver $layerResolver
    ) {
        $this->searchQuery           = $searchQuery;
        $this->searchFilter          = $searchFilter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->layerResolver         = $layerResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);

        $searchCriteria = $this->getSearchCriteria($field, $args);
        $searchResult   = $this->searchQuery->getResult($searchCriteria, $info);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getProductsSearchResult(),
            'page_info'   => $this->getPageInfo($searchCriteria),
            'layer_type'  => $this->getLayerType($args)
        ];
    }

    /**
     * Validate query argument and throwns an exception if something goes wrong.
     *
     * @param array $args
     *
     * @throws GraphQlInputException
     */
    private function validateArgs(array $args)
    {
        if ($args && !isset($args['search']) && !isset($args['filter'])) {
            throw new GraphQlInputException(__("'search' or 'filter' input argument is required."));
        }
    }

    /**
     * Build a search criteria from the request.
     *
     * @param Field $field
     * @param array $args
     *
     * @return SearchCriteriaInterface
     */
    private function getSearchCriteria(Field $field, array $args): SearchCriteriaInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder->build($field->getName(), $args);

        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        $searchCriteria->setRequestName($this->getSearchRequestName($args));

        if (isset($args['search'])) {
            $this->searchFilter->add($args['search'], $searchCriteria);
        }

        $this->prepareLayer($args);

        return $searchCriteria;
    }

    /**
     * Return search request name from arguments.
     *
     * @param array $args
     *
     * @return string
     */
    private function getSearchRequestName(array $args): string
    {
        return isset($args['search']) ? 'quick_search_container' : 'catalog_view_container';
    }

    /**
     * Return layer type from the argument.
     *
     * @param array $args
     *
     * @return string
     */
    private function getLayerType(array $args): string
    {
        return isset($args['search']) ? LayerResolver::CATALOG_LAYER_SEARCH : LayerResolver::CATALOG_LAYER_CATEGORY;
    }

    /**
     * Read page info from the search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return int[]
     */
    private function getPageInfo(SearchCriteriaInterface $searchCriteria): array
    {
        return ['page_size' => $searchCriteria->getPageSize(), 'current_page' => $searchCriteria->getCurrentPage()];
    }

    /**
     * Prepare the search layer to apply the current search.
     *
     * @param array $args
     */
    private function prepareLayer(array $args): void
    {
        if (isset($args['filter']) && isset($args['filter']['category_id'])) {
            $categoryId = $args['filter']['category_id']['eq'];
            $layerType  = $this->getLayerType($args);
            $this->layerResolver->get($layerType)->setCurrentCategory($categoryId);
        }
    }
}
