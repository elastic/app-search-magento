<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Indexer;

use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Elastic\AppSearch\Model\Adapter\EngineManagerInterface;
use Elastic\AppSearch\Model\Adapter\EngineResolverInterface;

/**
 * Implementation of the App Search index structure.
 *
 * @package   Elastic\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class IndexStructure implements IndexStructureInterface
{
    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var EngineManagerInterface
     */
    private $engineManager;

    /**
     * @var EngineManagerInterface
     */
    private $engineResolver;

    /**
     * Constructor.
     *
     * @param EngineManagerInterface  $engineManager
     * @param EngineResolverInterface $engineResolver
     * @param ScopeResolverInterface  $scopeResolver
     */
    public function __construct(
        EngineManagerInterface $engineManager,
        EngineResolverInterface $engineResolver,
        ScopeResolverInterface $scopeResolver
    ) {
        $this->scopeResolver  = $scopeResolver;
        $this->engineManager  = $engineManager;
        $this->engineResolver = $engineResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function create($index, array $fields, array $dimensions = [])
    {
        $storeId = $this->scopeResolver->getScope(current($dimensions)->getValue())->getId();
        $engine  = $this->engineResolver->getEngine($index, $storeId);

        if ($this->engineManager->engineExists($engine) === false) {
            $this->engineManager->createEngine($engine);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete($index, array $dimensions = [])
    {
        // TODO: implementation.
    }
}
