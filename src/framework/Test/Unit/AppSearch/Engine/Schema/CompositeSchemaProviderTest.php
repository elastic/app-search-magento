<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\Schema;

use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Schema;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Schema\Builder as SchemaBuilder;
use Elastic\AppSearch\Framework\AppSearch\Engine\Schema\CompositeSchemaProvider;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\Engine\Schema\CompositeSchemaProvider class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CompositeSchemaProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test ability to merge two schema using the composite schema provider implementation.
     *
     * @return void
     */
    public function testMergeSchema()
    {
        $providers = [
            $this->createSchemaProvider(['foo' => SchemaInterface::FIELD_TYPE_TEXT]),
            $this->createSchemaProvider(['bar' => SchemaInterface::FIELD_TYPE_TEXT]),
        ];

        $schema = (new CompositeSchemaProvider($this->getSchemaBuilder(), $providers))->getSchema();

        $this->assertCount(2, $schema->getFields());
        $this->assertArrayHasKey('foo', $schema->getFields());
        $this->assertArrayHasKey('bar', $schema->getFields());
    }

    /**
     * Create a schema provider from a static list of fields.
     *
     * @param array $fields Field of the schema.
     *
     * @return SchemaProviderInterface
     */
    private function createSchemaProvider(array $fields)
    {
        return new class($fields) implements SchemaProviderInterface {
            /**
             * @var array
             */
            private $fields;

            /**
             * @param array $fields
             */
            public function __construct(array $fields)
            {
                $this->fields = $fields;
            }

            /**
             * {@inheritDoc}
             */
            public function getSchema(): SchemaInterface
            {
                return new Schema($this->fields);
            }
        };
    }

    /**
     * Init the schema builder used during tests.
     *
     * @return SchemaBuilder
     */
    private function getSchemaBuilder()
    {
        $createSchema = function ($params) {
            return new Schema($params['fields']);
        };

        $schemaFactory = $this->createMock('Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterfaceFactory');
        $schemaFactory->expects($this->any())->method('create')->willReturnCallback($createSchema);

        return new SchemaBuilder($schemaFactory);
    }
}
