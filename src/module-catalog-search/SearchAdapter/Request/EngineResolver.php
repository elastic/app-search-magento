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
use Elastic\AppSearch\Framework\AppSearch\EngineResolverInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;

/**
 * Resolve search request engine.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter\Request
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor.
     *
     * @param EngineResolverInterface       $engineResolver
     * @param ScopeResolverInterface        $scopeResolver
     * @param StoreManagerInterface         $storeManager
     */
    public function __construct(
        EngineResolverInterface $engineResolver,
        ScopeResolverInterface $scopeResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->engineResolver = $engineResolver;
        $this->scopeResolver  = $scopeResolver;
        $this->storeManager   = $storeManager;
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
        return $this->engineResolver->getEngine($request->getIndex(), $this->getStoreId($request));
    }

    /**
     * Resolve store id from the search request.
     *
     * @param RequestInterface $request
     *
     * @return int
     */
    private function getStoreId(RequestInterface $request): int
    {
        $dimension = current($request->getDimensions());
        $storeId   = $this->scopeResolver->getScope($dimension->getValue())->getId();

        if ($storeId == 0) {
            $storeId = $this->storeManager->getDefaultStoreView()->getId();
        }

        return $storeId;
    }
}
