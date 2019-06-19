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
    public function getFieldName(AttributeAdapterInterface $attribute, array $context = []): string
    {
        $fieldName = $attribute->getAttributeCode();

        if (isset($context['type']) && $this->useValueField($attribute, $context['type'])) {
            $fieldName = $fieldName . self::VALUE_SUFFIX;
        }

        return $fieldName;
    }

    /**
     * Check if the attribute need a _value suffix for the current context.
     *
     * @param AttributeAdapterInterface $attribute
     * @param array                     $context
     *
     * @return boolean
     */
    private function useValueField(AttributeAdapterInterface $attribute, string $type)
    {
        $useValueField = false;

        $frontendType = $attribute->getFrontendInput();

        if ($frontendType == "boolean") {
            $useValueField = $type == SchemaInterface::CONTEXT_SEARCH;
        } elseif (in_array($frontendType, ['select', 'multiselect'])) {
            $useValueField = in_array($type, [SchemaInterface::CONTEXT_SEARCH, SchemaInterface::CONTEXT_SORT]);
        }

        return $useValueField && ($attribute->isSearchable() || $attribute->isSortable());
    }
}
