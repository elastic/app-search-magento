<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Fulltext;

use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\QueryLocatorInterface;

/**
 * Query text resolver implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Fulltext
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class QueryTextResolver implements QueryTextResolverInterface
{
    /**
     * @var QueryLocatorInterface
     */
    private $queryLocator;

    /**
     * Constructor.
     *
     * @param QueryLocatorInterface $queryLocator
     */
    public function __construct(QueryLocatorInterface $queryLocator)
    {
        $this->queryLocator = $queryLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function getText(RequestInterface $request): string
    {
        $query = $this->queryLocator->getQuery($request);

        return $query ? (string) $query->getValue() : '';
    }
}
