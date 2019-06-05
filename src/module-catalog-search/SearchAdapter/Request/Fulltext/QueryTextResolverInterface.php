<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Fulltext;

use Magento\Framework\Search\RequestInterface;

/**
 * Extract searched text from the search request.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Fulltext
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface QueryTextResolverInterface
{
    /**
     * Return searched text extracted from the request.
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    public function getText(RequestInterface $request): string;
}
