<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Search\Test\Unit\Model\Search;

use Elastic\AppSearch\Search\Model\Search\RequestBuilder;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;

/**
 * Unit test for the Elastic\AppSearch\Search\Model\Search\RequestBuilder class.
 *
 * @package   Elastic\AppSearch\Search\Test\Unit\Model\Search
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RequestBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test creating a request from a search criteria.
     */
    public function testCreateRequest()
    {
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $requestBuilder = $this->createRequestBuilder();

        $this->assertInstanceOf(RequestInterface::class, $requestBuilder->create($searchCriteria));
    }

    /**
     * Init request builder used during test.
     *
     * @return RequestBuilder
     */
    private function createRequestBuilder()
    {
        $request = $this->createMock(RequestInterface::class);
        $scope   = $this->createMock(\Magento\Framework\App\ScopeInterface::class);

        $builder = $this->createMock(\Elastic\AppSearch\Framework\Search\Request\Builder::class);
        $builder->expects($this->once())->method('create')->willReturn($request);
        $builder->expects($this->once())->method('bindDimension');
        $builder->expects($this->once())->method('setRequestName');
        $builder->expects($this->once())->method('setFrom');
        $builder->expects($this->once())->method('setSize');
        $builder->expects($this->once())->method('setSort');
        $builder->expects($this->once())->method('bind');

        $scopeResolver = $this->createMock(\Magento\Framework\App\ScopeResolverInterface::class);
        $scopeResolver->expects($this->once())->method('getScope')->willReturn($scope);

        $scopeConfig   = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        return new RequestBuilder($builder, $scopeResolver, $scopeConfig);
    }
}
