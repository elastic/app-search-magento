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

use Elastic\AppSearch\Framework\AppSearch\Engine\Schema\Builder as SchemaBuilder;
use Elastic\AppSearch\Framework\AppSearch\Engine\Schema;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
/**
 * Unit test for the Builder class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class BuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test building an empty schema.
     *
     * @return void
     */
    public function testBuildEmptySchema()
    {
        $schema = $this->getSchemaBuilder()->build();

        $this->assertInstanceOf(SchemaInterface::class, $schema);
        $this->assertEmpty($schema->getFields());
    }

    /**
     * Test adding a field into the schema.
     *
     *
     * @testWith ["foo", "text"]
     *
     * @param string $fieldName
     * @param string $fieldType
     *
     * @return void
     */
    public function testAddField($fieldName, $fieldType)
    {
        $schemaBuilder = $this->getSchemaBuilder();
        $schemaBuilder->addField($fieldName, $fieldType);

        $schema = $schemaBuilder->build();

        $this->assertInstanceOf(SchemaInterface::class, $schema);

        $this->assertCount(1, $schema->getFields());
        $this->assertArrayHasKey($fieldName, $schema->getFields());
        $this->assertEquals($fieldType, $schema->getFields()[$fieldName]);
    }

    /**
     * Test adding some fields into the schema.
     *
     * @testWith [{"foo": "text", "bar": "number"}]
     *
     * @param array $fields
     *
     * @return void
     */
    public function testAddFields($fields)
    {
        $schemaBuilder = $this->getSchemaBuilder();
        $schemaBuilder->addFields($fields);

        $schema = $schemaBuilder->build();

        $this->assertInstanceOf(SchemaInterface::class, $schema);

        $this->assertCount(count($fields), $schema->getFields());

        foreach ($fields as $fieldName => $fieldType) {
            $this->assertArrayHasKey($fieldName, $schema->getFields());
            $this->assertEquals($fieldType, $schema->getFields()[$fieldName]);
        }
    }

    /**
     * Init the schema builder used during tests.
     *
     * @return \Elastic\AppSearch\Framework\AppSearch\Engine\Schema\Builder
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
