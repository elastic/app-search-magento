<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Adapter\Engine;

use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SchemaTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test expected language mapping.
     *
     * @param array $fields
     *
     * @testWith [{"foo": "text", "bar": "number"}]
     */
    public function testCreateSchema($fields)
    {
        $schema = new Schema($fields);

        $this->assertEquals($fields, $schema->getFields());
    }
}
