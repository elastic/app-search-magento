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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\UniversalFactory;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\FieldMapperInterface;

/**
 * Implementation of the FilterBuilderInterface.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter
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
     * @param FieldMapperInterface $fieldMapper
     * @param UniversalFactory[]   $builderFactories
     */
    public function __construct(FieldMapperInterface $fieldMapper, array $builderFactories = [])
    {
        $this->builders = array_map(
            function ($factory) use ($fieldMapper) {
                return $this->createBuilder($factory, $fieldMapper);
            },
            $builderFactories
        );
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

    /**
     * Create a filter builder instance.
     *
     * @param UniversalFactory     $factory
     * @param FieldMapperInterface fieldMapper
     *
     * @return FilterBuilderInterface
     */
    private function createBuilder($factory, $fieldMapper): FilterBuilderInterface
    {
        return $factory->create(['fieldMapper' => $fieldMapper, 'filterBuilder' => $this]);
    }
}
