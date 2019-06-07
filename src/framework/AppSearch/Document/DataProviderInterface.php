<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Document;

/**
 * Retrive data for an entity to be indexed.
 *
 * @package   Elastic\Model\Adapter\Document
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface DataProviderInterface
{
    /**
     * Retrieve the data for a list of entity.
     *
     * @param array $entityIds
     * @param int   $storeId
     *
     * @return array
     */
    public function getData(array $entityIds, int $storeId): array;
}
