<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request;

use Magento\Framework\Search\RequestInterface;

/**
 * Retrieve AppSearch search request params.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface SearchParamsProviderInterface
{
    /**
     * Transform search request into a search param array.
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    public function getParams(RequestInterface $request): array;
}
