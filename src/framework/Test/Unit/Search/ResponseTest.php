<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\Search;

use Elastic\AppSearch\Framework\Search\Response;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for the Elastic\AppSearch\Framework\Search\Response class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\Search
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test doc count in response.
     */
    public function testGetCount()
    {
        $objectManager = new ObjectManager($this);

        $args     = $objectManager->getConstructArguments(Response::class, ['documents' => [], 'count' => 1]);
        $response = $objectManager->getObject(Response::class, $args);

        $this->assertEquals(1, $response->count());
    }
}
