<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Page;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Page\SearchParamsProvider;
use Magento\Framework\Search\RequestInterface;

/**
 * Unit test for the SearchParamsProvider class.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Page
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class SearchParamsProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test page params is correct accroos various request config.
     *
     * @param int $requestFrom
     * @param int $requestSize
     * @param int $currentPage
     * @param int $pageSize
     *
     * @testWith [0, 10, 1, 10]
     *           [null, 10, 1, 10]
     *           [0, null, 1, 100]
     *           [null, null, 1, 100]
     *           [null, 110, 1, 100]
     *           [10, 110, 1, 100]
     *           [11, 10, 2, 10]
     */
    public function testPageParams($requestFrom, $requestSize, $currentPage, $pageSize)
    {
        $searchParamsProvider = new SearchParamsProvider();

        $request = $this->createMock(RequestInterface::class);
        $request->method('getFrom')->willReturn($requestFrom);
        $request->method('getSize')->willReturn($requestSize);

        $pageParams = $searchParamsProvider->getParams($request);

        $this->assertArrayHasKey('page', $pageParams);
        $this->assertArrayHasKey('current', $pageParams['page']);
        $this->assertEquals($currentPage, $pageParams['page']['current']);

        $this->assertArrayHasKey('size', $pageParams['page']);
        $this->assertEquals($pageSize, $pageParams['page']['size']);
    }
}
