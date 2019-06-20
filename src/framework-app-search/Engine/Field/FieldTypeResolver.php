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
 * Default implementation of the field type resolver.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldTypeResolver implements FieldTypeResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFieldType(FieldInterface $field): string
    {
        return $field->getType() ?: SchemaInterface::FIELD_TYPE_TEXT;
    }
}
