<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter;

use Magento\Framework\Search\Request\FilterInterface;

/**
 * Extract and build filters from search request filters (\Magento\Framework\Search\Request\FilterInterface).
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FilterBuilderInterface
{
    /**
     * Build the filter array from the filter.
     *
     * @param FilterInterface $filter
     *
     * @return array
     */
    public function getFilter(FilterInterface $filter): array;
}
