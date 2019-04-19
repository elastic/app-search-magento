<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema\Product;

/**
 * Price fields for the product schema.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class PriceSchemaProvider extends AbstractSchemaProvider
{

    protected function getAttributesData()
    {
        return [
            ['attribute_code' => 'price', 'backend_type' => 'decimal'],
            ['attribute_code' => 'customer_group_id'],
        ];
    }
}
