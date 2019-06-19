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
 * Field mapper interface.
 *
 * @api
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldMapperInterface
{
    /**
     * Return field name to use for the attribute.
     *
     * @param string $attribute
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
