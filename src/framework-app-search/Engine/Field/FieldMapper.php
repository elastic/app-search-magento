<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field;

/**
 * Default field mapper implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldMapper implements FieldMapperInterface
{
    /**
     * @var FieldProviderInterface
     */
    private $fieldProvider;

    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var FieldTypeResolverInterface
     */
    private $fieldTypeResolver;

    /**
     * @var FieldValueMapperInterface[]
     */
    private $fieldValueMappers;

    /**
     * Constructor.
     *
     * @param FieldProviderInterface      $fieldProvider
     * @param FieldNameResolverInterface  $fieldNameResolver
     * @param FieldTypeResolverInterface  $fieldTypeResolver
     * @param FieldValueMapperInterface[] $fieldValueMappers
     */
    public function __construct(
        FieldProviderInterface $fieldProvider,
        FieldNameResolverInterface $fieldNameResolver,
        FieldTypeResolverInterface $fieldTypeResolver,
        array $fieldValueMappers = []
    ) {
        $this->fieldProvider     = $fieldProvider;
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldTypeResolver = $fieldTypeResolver;
        $this->fieldValueMappers = $fieldValueMappers;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldName(string $fieldName, array $context = []): string
    {
        return $this->fieldNameResolver->getFieldName($this->getField($fieldName), $context);
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldType(string $fieldName): string
    {
        return $this->fieldTypeResolver->getFieldType($this->getField($fieldName));
    }

    /**
     * {@inheritDoc}
     */
    public function mapValue($fieldName, $value)
    {
        $fieldType = $this->getFieldType($fieldName);

        if (isset($this->fieldValueMappers[$fieldType])) {
            $value = $this->fieldValueMappers[$fieldType]->mapValue($value);
        }

        return $value;
    }

    private function getField($fieldName): FieldInterface
    {
        return $this->fieldProvider->getField($fieldName);
    }
}
