<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldName;

use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapter;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;

/**
 * Used to retrieve field name from an attribute depending on the context.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DefaultResolver implements FieldNameResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFieldName(AttributeAdapter $attribute, array $context = []): ?string
    {
        $fieldName = $attribute->getAttributeCode();

        if (isset($context['type']) && $this->useValueField($attribute, $context['type'])) {
            $fieldName = $fieldName . '_value';
        }

        return $fieldName;
    }

    /**
     * Check if the attribute need a _value suffix for the current context.
     *
     * @param AttributeAdapter $attribute
     * @param array            $context
     *
     * @return boolean
     */
    private function useValueField(AttributeAdapter $attribute, string $type)
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
