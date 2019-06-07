<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor;

use Magento\Framework\Search\RequestInterface;

/**
 * Aggregate multiple search params providers.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor
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
     * @var RescorerResolverInterface
     */
    private $rescorerResolver;

    /**
     * Constructor.
     *
     * @param RescorerResolverInterface       $rescorerResolver
     * @param SearchParamsProviderInterface[] $providers
     */
    public function __construct(RescorerResolverInterface $rescorerResolver, array $providers = [])
    {
        $this->providers        = $providers;
        $this->rescorerResolver = $rescorerResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        return $this->getRescorer($request)->prepareSearchParams($request, $this->collectSearchParams($request));
    }

    /**
     * Collect search params from child providers.
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    private function collectSearchParams(RequestInterface $request): array
    {
        $searchParams = array_map(
            function (SearchParamsProviderInterface $provider) use ($request) {
                return $provider->getParams($request);
            },
            $this->providers
        );

        return !empty($searchParams) ? array_merge_recursive(...array_values($searchParams)) : [];
    }

    /**
     * Get rescorer for the current search request.
     *
     * @param RequestInterface $request
     *
     * @return RescorerInterface
     */
    private function getRescorer(RequestInterface $request): RescorerInterface
    {
        return $this->rescorerResolver->getRescorer($request);
    }
}
