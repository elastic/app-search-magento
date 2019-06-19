<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Search\Test\Unit;

use Elastic\AppSearch\Framework\Search\Request;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for the Request class.
 *
 * @package   Elastic\AppSearch\Framework\Search\Test\Unit
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test doc sort order in request.
     */
    public function testGetSortOrder()
    {
        $objectManager = new ObjectManager($this);

        $sortOrder = $this->createMock(\Magento\Framework\Api\SortOrder::class);

        $args     = $objectManager->getConstructArguments(Request::class, ['sort' => [$sortOrder]]);
        $request  = $objectManager->getObject(Request::class, $args);

        $this->assertCount(1, $request->getSort());
        $this->assertEquals($sortOrder, current($request->getSort()));
    }
}
