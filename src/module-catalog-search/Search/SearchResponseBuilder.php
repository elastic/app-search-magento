<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Search;

use Magento\Framework\Search\ResponseInterface;

/**
 * Custome search response builder (use document count from the response instead of countinhg results array).
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Search
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchResponseBuilder extends \Magento\Framework\Search\SearchResponseBuilder
{
    /**
     * {@inheritDoc}
     */
    public function build(ResponseInterface $response)
    {
        $searchResult = parent::build($response);
        $searchResult->setTotalCount($response->count());

        return $searchResult;
    }
}
