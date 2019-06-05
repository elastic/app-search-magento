<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet;

use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet\FacetBuilderInterfaceFactory;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\FieldMapperResolverInterface;

/**
 * Extract and build facets from the search request.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var FacetBuilderInterfaceFactory
     */
    private $facetBuilderFactory;

    /**
     * @var FieldMapperResolverInterface
     */
    private $fieldMapperResolver;

    /**
     * @var FacetBuilderInterface
     */
    private $builders = [];

    /**
     * Constructor.
     *
     * @param FieldMapperResolverInterface $fieldMapperResolver
     * @param FacetBuilderInterfaceFactory $facetBuilderFactory
     */
    public function __construct(
        FieldMapperResolverInterface $fieldMapperResolver,
        FacetBuilderInterfaceFactory $facetBuilderFactory
    ) {
        $this->fieldMapperResolver = $fieldMapperResolver;
        $this->facetBuilderFactory = $facetBuilderFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $facets = [];

        foreach ($request->getAggregation() as $aggregation) {
            $index  = $request->getIndex();
            $facets = array_merge_recursive($facets, $this->getFacetBuilder($index)->getFacet($aggregation));
        }

        return !empty($facets) ? ['facets' => $facets] : [];
    }

    private function getFacetBuilder(string $index)
    {
        if (!isset($this->builders[$index])) {
            $fieldMapper = $this->fieldMapperResolver->getFieldMapper($index);
            $this->builders[$index] = $this->facetBuilderFactory->create(['fieldMapper' => $fieldMapper]);
        }

        return $this->builders[$index];
    }
}
