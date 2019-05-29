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
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
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
                    $facets[$facet['name']] = $this->parseFacetValues($facet['data']);
                }
            }
        }

        $response['facets'] = $facets;

        return $response;
    }

    /**
     * Parse facet values to match response format.
     *
     * @param array $values
     *
     * @return array
     */
    private function parseFacetValues(array $values): array
    {
        return $this->filterFacetValues(array_map([$this, 'parseFacetValue'], $values));
    }

    /**
     * Remove facet values when the value is empty or the count is 0.
     *
     * @param array $values
     *
     * @return array
     */
    private function filterFacetValues(array $values): array
    {
        return array_filter($values, function ($value) {
            return !empty($value['value']) && $value['count'] > 0;
        });
    }

    /**
     * Parse facet value (mosty convert range into string).
     *
     * @param array $value
     *
     * @return array
     */
    private function parseFacetValue(array $value)
    {
        if (isset($value['from']) || isset($value['to'])) {
            $value = ['value' => $this->getRangeValueString($value), 'count' => $value['count']];
        }

        return $value;
    }

    /**
     * Get string representation for range facet value.
     *
     * @param array $value
     *
     * @return string
     */
    private function getRangeValueString(array $value): string
    {
        return sprintf("%s_%s", $value['from'] ?? '', $value['to'] ?? '');
    }
}
