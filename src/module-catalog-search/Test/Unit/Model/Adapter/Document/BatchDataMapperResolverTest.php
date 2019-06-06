<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Adapter\Document;

use Elastic\AppSearch\CatalogSearch\Model\Adapter\Document\BatchDataMapperResolver;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Document\BatchDataMapperInterface;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\Model\Adapter\Document\BatchDataMapperResolver class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class BatchDataMapperResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Retrieve data mapper and test the result is valid
     */
    public function testGetValidMapper()
    {
        $mapper = $this->getResolver()->getMapper("foo");
        $this->assertInstanceOf(BatchDataMapperInterface::class, $mapper);
    }

    /**
     * Check an exceptiion is thrown when an invalid mapper is requested.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testGetInvalidMapper()
    {
        $mapper = $this->getResolver()->getMapper("invalid");
        $this->assertInstanceOf(BatchDataMapperInterface::class, $mapper);
    }

    private function getResolver()
    {
        $mappers = ['foo' => $this->createMock(BatchDataMapperInterface::class)];

        return new BatchDataMapperResolver($mappers);
    }
}