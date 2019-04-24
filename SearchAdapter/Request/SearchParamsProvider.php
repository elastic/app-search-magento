<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request;

use Magento\Framework\Search\RequestInterface;

/**
 * Aggregate multiple search params providers.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var SearchParamsProviderInterface[]
     */
    private $providers;

    /**
     * Constructor.
     *
     * @param SearchParamsProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $searchParams = array_map(
            function (SearchParamsProviderInterface $provider) use ($request) {
                return $provider->getParams($request);
            },
            $this->providers
        );

        return !empty($searchParams) ? array_merge_recursive(...array_values($searchParams)) : [];
    }
}
