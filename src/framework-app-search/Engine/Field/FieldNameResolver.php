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

use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;

/**
 * Default field name resolver implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldNameResolver implements FieldNameResolverInterface
{
    /**
     * @var string
     */
    private const VALUE_SUFFIX = '_value';

    /**
     * {@inheritDoc}
     */
    public function getFieldName(FieldInterface $field, array $context = []): string
    {
        $fieldName = $field->getName();

        if (isset($context['type']) && $this->useValueField($field, $context['type'])) {
            $fieldName = $fieldName . self::VALUE_SUFFIX;
        }

        return $fieldName;
    }

    /**
     * Check if the field need a _value suffix for the current context.
     *
     * @param FieldInterface $field
     * @param array          $context
     *
     * @return boolean
     */
    private function useValueField(FieldInterface $field, string $type)
    {
        return $field->useValueField() && in_array($type, [SchemaInterface::CONTEXT_SEARCH, SchemaInterface::CONTEXT_SORT]);
    }
}
