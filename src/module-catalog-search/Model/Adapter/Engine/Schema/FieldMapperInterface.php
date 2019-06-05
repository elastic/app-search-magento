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
 * Field mapper interface.
 *
 * @package   Elastic\Model\Adapter\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldMapperInterface
{
    /**
     * Return field name to use for the attribute.
     *
     * @param AttributeAdapter $attribute
     *
     * @return string
     */
    public function getFieldName(string $attributeCode, array $context = []): string;

    /**
     * Return field type to use to index the attribute.
     *
     * @param string $attributeCode
     *
     * @return string
     */
    public function getFieldType(string $attributeCode): string;

    /**
     * Prepare a value to be indexed.
     *
     * @param string $attributeCode
     * @param mixed  $value
     *
     * @return mixed
     */
    public function mapValue($attributeCode, $value);
}
