<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema\Product;


use Elastic\AppSearch\Model\Adapter\Engine\SchemaProviderInterface;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\BuilderInterface;

/**
 * A temporary schema provider for products that will be replaced by the real implementation.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DummySchemaProvider implements SchemaProviderInterface
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * Constructor.
     *
     * @param BuilderInterface $builder
     */
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        $this->builder->addField('test', SchemaInterface::FIELD_TYPE_TEXT);

        return $this->builder->build();
    }
}
