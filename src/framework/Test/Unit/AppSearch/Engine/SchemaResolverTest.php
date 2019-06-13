<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine;

use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaResolver;

/**
 * Unit test for the SchemaResolver class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SchemaResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test getting a schema.
     *
     * @return void
     */
    public function testGetSchema()
    {
        $schema = $this->getSchemaResolver()->getSchema('engine_name');

        $this->assertInstanceOf(SchemaInterface::class, $schema);
    }

    /**
     * Test getting a schema when no provider have been registred.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    public function testGetInvalidSchema()
    {
        $this->getSchemaResolver()->getSchema('invalid_engine_name');
    }

    /**
     * Init the schema resolver used in tests.
     *
     * @return SchemaResolver
     */
    private function getSchemaResolver()
    {
        $schemaMock = $this->createMock(SchemaInterface::class);
        $schemaProviderMock = $this->createMock(SchemaProviderInterface::class);
        $schemaProviderMock->method('getSchema')->willReturn($schemaMock);
        $providers = ['engine_name' => $schemaProviderMock];

        return new SchemaResolver($providers);
    }
}
