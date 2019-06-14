<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogGraphQl\Model\ResourceModel\Product;

use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * A product collection that can be instantiated using search result.
 * Used to wrap faceted data into a product collection in GraphQL layer resolver.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\CatalogGraphQl\Model\ResourceModel\Product
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchResultCollection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * Set search result into the collection.
     *
     * @param SearchResultInterface $searchResult
     */
    public function setSearchResult(SearchResultInterface $searchResult)
    {
        $this->_totalRecords = $searchResult->getTotalCount();
        $this->aggregations  = $searchResult->getAggregations();
    }

    /**
     * Return field faceted data from faceted search result.
     *
     * @param string $field
     *
     * @return array
     */
    public function getFacetedData($field)
    {
        $result = [];

        if (null !== $this->aggregations) {
            $bucket = $this->aggregations->getBucket($field . RequestGenerator::BUCKET_SUFFIX);
            if ($bucket) {
                foreach ($bucket->getValues() as $value) {
                    $metrics = $value->getMetrics();
                    $result[$metrics['value']] = $metrics;
                }
            }
        }

        return $result;
    }
}
