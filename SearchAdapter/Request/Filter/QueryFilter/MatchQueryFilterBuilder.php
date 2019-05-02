<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilter;

use Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilterBuilderInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Elastic\AppSearch\SearchAdapter\Request\QueryLocatorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Implementation of the match query filter builder.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter\QueryFilter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class MatchQueryFilterBuilder implements QueryFilterBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFilter(QueryInterface $query): array
    {
        if ($query->getName() !== QueryLocatorInterface::FULLTEXT_QUERY_NAME) {
            throw new LocalizedException(
                __("Query can contains only one match query with name %1", QueryLocatorInterface::FULLTEXT_QUERY_NAME)
            );
        }

        return [];
    }
}
