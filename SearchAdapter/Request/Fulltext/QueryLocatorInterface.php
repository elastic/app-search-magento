<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Fulltext;

use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface;

/**
 * Extract the fulltext match query from the request query.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Fulltext
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface QueryLocatorInterface
{
    /**
     * @var string
     */
    const FULLTEXT_QUERY_NAME = "search";

    /**
     * Extract the fulltext query from the request
     *
     * @param RequestInterface $request
     *
     * @return QueryInterface|NULL
     */
    public function getQuery(RequestInterface $request): ?QueryInterface;
}
