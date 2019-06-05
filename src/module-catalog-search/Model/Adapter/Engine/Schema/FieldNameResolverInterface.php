<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema;

/**
 * Used to retrieve field name from an attribute depending on the context.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldNameResolverInterface
{
    /**
     * Return field name to use for the attribute. Real field name depends on the context (search, filter, ...).
     *
     * @param AttributeAdapter $attribute
     * @param array $context
     *
     * @return string|NULL
     */
    public function getFieldName(AttributeAdapter $attribute, array $context = []): ?string;
}
