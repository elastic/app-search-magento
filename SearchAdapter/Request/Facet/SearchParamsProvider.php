<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Facet;

use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;

/**
 * Extract and build facets from the search request.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Facet
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var FacetBuilderInterface
     */
    private $facetBuilder;

    /**
     * Constructor.
     *
     * @param FacetBuilderInterface $facetBuilder
     */
    public function __construct(FacetBuilderInterface $facetBuilder)
    {
        $this->facetBuilder = $facetBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $facets = [];

        foreach ($request->getAggregation() as $aggregation) {
            $facets = array_merge_recursive($facets, $this->facetBuilder->getFacet($aggregation));
        }

        return !empty($facets) ? ['facets' => $facets] : [];
    }
}
