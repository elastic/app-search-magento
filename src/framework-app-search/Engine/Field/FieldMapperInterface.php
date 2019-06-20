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
     * Return field name you can use depending of the context.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getFieldName(string $fieldName, array $context = []): string;

    /**
     * Return field type.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getFieldType(string $fieldName): string;

    /**
     * Prepare a value to be indexed.
     *
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return mixed
     */
    public function mapValue($fieldName, $value);
}
