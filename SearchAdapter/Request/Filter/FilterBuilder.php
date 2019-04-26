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

use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Implementation of the FilterBuilderInterface.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FilterBuilder implements FilterBuilderInterface
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
    public function getFilter(FilterInterface $filter): array
    {
        if (!isset($this->builders[$filter->getType()])) {
            throw new LocalizedException(
                __("Unable to find query builder for filter with type %1", $filter->getType())
            );
        }

        return $this->builders[$filter->getType()]->getFilter($filter);
    }
}
