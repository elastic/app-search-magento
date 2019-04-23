<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Document\BatchDataMapper\Product;

use Elastic\AppSearch\Model\Adapter\Document\DataProviderInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapterProvider as AttributeProvider;

/**
 * Retrive data for an product to be indexed.
 *
 * @package   Elastic\Model\BatchDataMapper\Product
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
abstract class AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var AttributeProvider
     */
    private $attributeProvider;

    /**
     * Constructor.
     *
     * @param AttributeProvider $attributeProvider
     * @param FieldNameResolverInterface $fieldNameResolver
     */
    public function __construct(AttributeProvider $attributeProvider, FieldNameResolverInterface $fieldNameResolver)
    {
        $this->attributeProvider = $attributeProvider;
        $this->fieldNameResolver = $fieldNameResolver;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function getData(array $entityIds, int $storeId): array;

    /**
     * Return index field name from a string.
     *
     * @param string $field
     *
     * @return string
     */
    protected function getFieldName(string $field): string
    {
        return $this->fieldNameResolver->getFieldName($this->attributeProvider->getAttributeAdapter($field));
    }
}
