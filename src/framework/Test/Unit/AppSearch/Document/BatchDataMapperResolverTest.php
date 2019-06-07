<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Document;

use Elastic\AppSearch\Framework\AppSearch\Document\BatchDataMapperResolver;
use Elastic\AppSearch\Framework\AppSearch\Document\BatchDataMapperInterface;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\Document\BatchDataMapperResolver class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Document
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
