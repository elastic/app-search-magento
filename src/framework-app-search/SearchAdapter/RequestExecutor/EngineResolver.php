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
use Elastic\AppSearch\Framework\AppSearch\EngineResolverInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;

/**
 * Resolve search request engine.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineResolver
{
    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * Constructor.
     *
     * @param EngineResolverInterface $engineResolver
     * @param ScopeResolverInterface  $scopeResolver
     */
    public function __construct(EngineResolverInterface $engineResolver, ScopeResolverInterface $scopeResolver)
    {
        $this->engineResolver = $engineResolver;
        $this->scopeResolver  = $scopeResolver;
    }

    /**
     * Resolve the engine of the current request.
     *
     * @param RequestInterface $request
     *
     * @return EngineInterface
     */
    public function getEngine(RequestInterface $request): EngineInterface
    {
        return $this->engineResolver->getEngine($request->getIndex(), $this->getScopeId($request));
    }

    /**
     * Resolve scope id from the search request.
     *
     * @param RequestInterface $request
     *
     * @return int
     */
    private function getScopeId(RequestInterface $request): int
    {
        $dimension = current($request->getDimensions());

        return $this->scopeResolver->getScope($dimension->getValue())->getId();
    }
}
