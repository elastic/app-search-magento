<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Schema;

/**
 * Price fields for the product schema.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\Product\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CategorySchemaProvider extends AbstractSchemaProvider
{
    /**
     * {@inheritDoc}
     */
    protected function getAttributesData()
    {
        return [
            ['attribute_code' => 'category_name'],
            ['attribute_code' => 'category_ids'],
        ];
    }
}
