<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema;


use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterfaceFactory;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;

/**
 * AppSearch Engine Schema builder implementation.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Builder implements BuilderInterface
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var SchemaInterfaceFactory
     */
    private $schemaFactory;

    /**
     * Constructor.
     *
     * @param SchemaInterfaceFactory
     */
    public function __construct(SchemaInterfaceFactory $schemaFactory)
    {
        $this->schemaFactory = $schemaFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function addField(string $fieldName, string $fieldType)
    {
        $this->fields[$fieldName] = $fieldType;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function build(): SchemaInterface
    {
        $schema = $this->schemaFactory->create(['fields' => $this->fields]);

        $this->fields = [];

        return $schema;
    }
}
