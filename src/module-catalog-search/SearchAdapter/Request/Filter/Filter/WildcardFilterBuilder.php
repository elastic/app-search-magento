<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\Filter;

use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\FilterBuilderInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Implementation of the wildcard filter builder.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class WildcardFilterBuilder implements FilterBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFilter(FilterInterface $filter): array
    {
        throw new LocalizedException(
            __('Wildcard filter used for field %1 is not supported by AppSearch.', $filter->getField())
        );
    }
}
