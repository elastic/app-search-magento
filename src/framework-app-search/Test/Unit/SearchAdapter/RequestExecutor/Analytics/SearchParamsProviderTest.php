<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Analytics;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Analytics\SearchParamsProvider;
use Magento\Framework\Search\RequestInterface;

/**
 * Unit test for the SearchParamsProvider class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Analytics
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class SearchParamsProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test analytics tags are added to the search params.
     */
    public function testGetSearchParams()
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getName')->willReturn('request_name');

        $paramsProvider = new SearchParamsProvider();

        $params = $paramsProvider->getParams($request);

        $this->assertEquals(['analytics' => ['tags' => ['request_name']]], $params);
    }
}
