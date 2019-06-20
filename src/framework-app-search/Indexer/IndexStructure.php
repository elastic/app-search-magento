<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Indexer;

use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineManagerInterface;
use Elastic\AppSearch\Framework\AppSearch\EngineResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaResolverInterface;

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
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * @var SchemaResolverInterface
     */
    private $schemaResolver;

    /**
     * Constructor.
     *
     * @param EngineManagerInterface  $engineManager
     * @param EngineResolverInterface $engineResolver
     * @param SchemaResolverInterface $schemaResolver
     * @param ScopeResolverInterface  $scopeResolver
     */
    public function __construct(
        EngineManagerInterface $engineManager,
        EngineResolverInterface $engineResolver,
        SchemaResolverInterface $schemaResolver,
        ScopeResolverInterface $scopeResolver
    ) {
        $this->engineManager  = $engineManager;
        $this->schemaResolver = $schemaResolver;
        $this->engineResolver = $engineResolver;
        $this->scopeResolver  = $scopeResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function create($index, array $fields, array $dimensions = [])
    {
        $storeId = $this->scopeResolver->getScope(current($dimensions)->getValue())->getId();
        $engine  = $this->engineResolver->getEngine($index, $storeId);

        // If the engine does not exists yet, create it.
        if ($this->engineManager->engineExists($engine) === false) {
            $this->engineManager->createEngine($engine);
        }

        // Update the schema of the engine.
        $schema = $this->schemaResolver->getSchema($index);
        $this->engineManager->updateSchema($engine, $schema);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($index, array $dimensions = [])
    {
        // TODO: implementation.
    }
}
