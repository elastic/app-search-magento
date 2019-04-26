<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Filter;

use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Implementation of QueryFilterBuilderInterface.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class QueryFilterBuilder implements QueryFilterBuilderInterface
{
    /**
     * @var QueryFilterBuilderInterface[]
     */
    private $builders = [];

    /**
     * Constructor.
     *
     * @param QueryFilterBuilderInterface[] $builders
     */
    public function __construct(array $builders = [])
    {
        $this->builders = $builders;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter(QueryInterface $query): array
    {
        if (!isset($this->builders[$query->getType()])) {
            throw new LocalizedException(
                __("Unable to find query builder for query with type %1", $query->getType())
            );
        }

        return $this->builders[$query->getType()]->getFilter($query);
    }
}
