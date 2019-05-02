<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Filter;

use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;

/**
 * Extract and build filters from the search request.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var QueryFilterBuilderInterface
     */
    private $queryFilterBuilder;

    /**
     * Constructor.
     *
     * @param QueryFilterBuilderInterface $queryFilterBuilder
     */
    public function __construct(QueryFilterBuilderInterface $queryFilterBuilder)
    {
        $this->queryFilterBuilder = $queryFilterBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $filters = $request->getQuery() ? $this->queryFilterBuilder->getFilter($request->getQuery()) : [];

        return !empty($filters) ? ['filters' => $filters] : [];
    }
}
