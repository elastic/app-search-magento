<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldType;

use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldTypeResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapter;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;

/**
 * Used to retrieve field type from an attribute.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DefaultResolver implements FieldTypeResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFieldType(AttributeAdapter $attribute): ?string
    {
        $fieldType = SchemaInterface::FIELD_TYPE_TEXT;

        if ($attribute->isNumberType()) {
            $fieldType = SchemaInterface::FIELD_TYPE_NUMBER;
        } elseif ($attribute->isDateType()) {
            $fieldType = SchemaInterface::FIELD_TYPE_DATE;
        }

        return $fieldType;
    }
}
