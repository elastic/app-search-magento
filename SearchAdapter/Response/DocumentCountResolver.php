<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Response;

/**
 * Retrieve document count from the App Search response
 *
 * @package   Elastic\AppSearch\SearchAdapter\Response
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DocumentCountResolver
{
    /**
     * Extract doc count from the response array.
     *
     * @param array $response
     *
     * @return int
     */
    public function getDocumentCount(array $response): int
    {
        return !empty($response['meta']['page']['total_results']) ? $response['meta']['page']['total_results'] : 0;
    }
}
