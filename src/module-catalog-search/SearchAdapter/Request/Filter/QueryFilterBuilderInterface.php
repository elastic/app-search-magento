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

use Magento\Framework\Search\Request\QueryInterface;

/**
 * Extract and build filters from search request queries (\Magento\Framework\Search\Request\QueryInterface).
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface QueryFilterBuilderInterface
{
    /**
     * Build the filter array from a query.
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function getFilter(QueryInterface $query): array;
}
