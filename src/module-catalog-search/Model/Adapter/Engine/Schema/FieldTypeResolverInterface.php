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
 * Used to retrieve field type from an attribute.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldTypeResolverInterface
{
    /**
     * Return field type to use for the attribute.
     *
     * @param AttributeAdapter $attribute
     *
     * @return string|NULL
     */
    public function getFieldType(AttributeAdapter $attribute): ?string;
}
