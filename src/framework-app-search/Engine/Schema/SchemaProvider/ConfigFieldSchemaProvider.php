<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Schema\SchemaProvider;

use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Schema\BuilderInterface as SchemaBuilderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldProviderInterface;

/**
 * Read field from a schema provider and create a schema from it.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Schema\SchemaProvider
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ConfigFieldSchemaProvider implements SchemaProviderInterface
{
    /**
     * @var SchemaBuilderInterface
     */
    private $schemaBuilder;

    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * @var FieldProviderInterface
     */
    private $fieldProvider;

    /**
     * Constructor.
     *
     * @param SchemaBuilderInterface $schemaBuilder
     * @param FieldMapperInterface   $fieldMapper
     * @param FieldProviderInterface $fieldProvider
     */
    public function __construct(
        SchemaBuilderInterface $schemaBuilder,
        FieldMapperInterface $fieldMapper,
        FieldProviderInterface $fieldProvider
    ) {
        $this->schemaBuilder = $schemaBuilder;
        $this->fieldMapper   = $fieldMapper;
        $this->fieldProvider = $fieldProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): SchemaInterface
    {
        foreach ($this->fieldProvider->getFields() as $field) {
            $fieldName = $this->fieldMapper->getFieldName($field->getName());
            $fieldType = $this->fieldMapper->getFieldType($field->getName());

            $this->schemaBuilder->addField($fieldName, $fieldType);

            if ($field->useValueField()) {
                $context = ['type' => SchemaInterface::CONTEXT_SEARCH];
                $fieldName = $this->fieldMapper->getFieldName($field->getName(), $context);
                $this->schemaBuilder->addField($fieldName, $fieldType);
            }
        }

        return $this->schemaBuilder->build();
    }
}
