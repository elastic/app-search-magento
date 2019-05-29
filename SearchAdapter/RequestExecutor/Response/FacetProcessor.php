<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\RequestExecutor\Response;

use Magento\Framework\Search\RequestInterface;

/**
 * Process facet from the App Search response.
 *
 * @package   Elastic\AppSearch\SearchAdapter\RequestExecutor\Response
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FacetProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(RequestInterface $request, array $response): array
    {
        return $this->addMissingFacets($request, $this->parseFacets($response));
    }

    /**
     * Add missing facet to the response.
     *
     * @param RequestInterface $request
     * @param array            $response
     *
     * @return array
     */
    private function addMissingFacets(RequestInterface $request, array $response): array
    {
        foreach ($request->getAggregation() as $bucket) {
            if (!isset($response['facets'][$bucket->getName()])) {
                $response['facets'][$bucket->getName()] = [];
            }
        }

        return $response;
    }

    /**
     * Parse result facets and convert into the format expected by the ResponseFactory.
     *
     * @param array $response
     *
     * @return array
     */
    private function parseFacets(array $response): array
    {
        $facets = [];

        if (isset($response['facets'])) {
            foreach ($response['facets'] as $fieldFacets) {
                foreach ($fieldFacets as $facet) {
                    $facets[$facet['name']] = $facet['data'];
                }
            }
        }

        $response['facets'] = $facets;

        return $response;
    }
}
