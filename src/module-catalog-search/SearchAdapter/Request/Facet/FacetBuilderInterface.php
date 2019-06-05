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

use Magento\Framework\Search\Request\BucketInterface;

/**
 * Extract and build facets from search request aggregation buckets.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Facet
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FacetBuilderInterface
{
    /**
     * Build the facet array from the bucket.
     *
     * @param BucketInterface $bucket
     *
     * @return array
     */
    public function getFacet(BucketInterface $bucket): array;
}
