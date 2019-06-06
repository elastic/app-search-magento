<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema;

use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaProviderInterface;

/**
 * Generate a schema as a result of the merge of several schemas.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CompositeSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var SchemaProviderInterface[]
     */
    private $providers;

    public function __construct(BuilderInterface $builder, array $providers)
    {
        $this->builder   = $builder;
        $this->providers = $providers;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        foreach ($this->providers as $schemaProvider) {
            $schema = $schemaProvider->getSchema();
            $this->builder->addFields($schema->getFields());
        }

        return $this->builder->build();
    }
}
