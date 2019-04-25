<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Filter\Filter;

use Elastic\AppSearch\SearchAdapter\Request\Filter\FilterBuilderInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapterProvider;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;

/**
 * Extract and build filters from the search request.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RangeFilterBuilder implements FilterBuilderInterface
{
    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var AttributeAdapterProvider
     */
    private $attributeProvider;

    /**
     * Constructor.
     *
     * @param AttributeAdapterProvider   $attributeProvider
     * @param FieldNameResolverInterface $fieldNameResolver
     */
    public function __construct(
        AttributeAdapterProvider $attributeProvider,
        FieldNameResolverInterface $fieldNameResolver
    ) {
            $this->attributeProvider = $attributeProvider;
            $this->fieldNameResolver = $fieldNameResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter(FilterInterface $filter): array
    {
        $fieldName = $this->getFieldName($filter->getField());

        $range = array_map('floatval', array_filter(
            ['from' => $filter->getFrom(), 'to' => $filter->getTo()]
        ));

        return !empty($range) ? [$fieldName => $range] : [];
    }

    /**
     * Convert the field name to match the indexed data.
     *
     * @param string $requestFieldName
     *
     * @return string
     */
    private function getFieldName(string $requestFieldName)
    {
        $attribute = $this->attributeProvider->getAttributeAdapter($requestFieldName);

        return $this->fieldNameResolver->getFieldName($attribute, ['type' => SchemaInterface::CONTEXT_FILTER]);
    }
}
