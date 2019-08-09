<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Magento\Framework\Component\ComponentRegistrar;

$canEnable = @ComponentRegistrar::getPath(ComponentRegistrar::MODULE, 'Magento_GraphQl') != null;

if ($canEnable) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'ElasticAppSearch_CatalogGraphQl', __DIR__);
}
