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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\UniversalFactory;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;

/**
 * Implementation of QueryFilterBuilderInterface.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter
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
    public function getFilter(QueryInterface $query): array
    {
        if (!isset($this->builders[$query->getType()])) {
            throw new LocalizedException(
                __("Unable to find query builder for query with type %1", $query->getType())
            );
        }

        return $this->builders[$query->getType()]->getFilter($query);
    }

    /**
     * Create a query filter builder instance.
     *
     * @param UniversalFactory     $factory
     * @param FieldMapperInterface $fieldMapper
     *
     * @return FilterBuilderInterface
     */
    private function createBuilder($factory, $fieldMapper): QueryFilterBuilderInterface
    {
        return $factory->create(['fieldMapper' => $fieldMapper, 'queryBuilder' => $this]);
    }
}
