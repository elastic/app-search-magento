<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Document;

/**
 * Map a collection of objects to searchable documents.
 *
 * @api
 *
 * @package   Elastic\Model\AdapterDocument
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface BatchDataMapperInterface
{
    /**
     * Convert the collection of objects into documents.
     *
     * @param array $documentData
     * @param int   $storeId
     *
     * @return array
     */
    public function map(array $documentData, int $storeId): array;
}
