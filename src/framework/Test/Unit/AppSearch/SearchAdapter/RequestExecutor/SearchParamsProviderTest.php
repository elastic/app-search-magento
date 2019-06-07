<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\SearchParamsProvider;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\SearchParamsProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\RescorerInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\RescorerResolverInterface;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\SearchParamsProvide class.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class SearchParamsProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $providersData = [
        'provider1' => ['foo' => ['foo' => 'foo 1', 'bar' => 'bar'], 'bar' => 'bar', 'baz' => 'baz'],
        'provider2' => ['foo' => ['foo' => 'foo 2', 'baz' => 'baz'], 'bar' => 'bar', 'qux' => 'qux'],
        'provider3' => ['empty' => []],
        'provider4' => ['null' => null],
        'provider5' => ['false' => false],
        'provider6' => ['zero' => 0],
        'provider7' => ['emptystring' => ""],
        'provider8' => [],
    ];

    /**
     * Test search params merging.
     */
    public function testGetSearchParams()
    {
        $searchParams = $this->getProvider()->getParams($this->createMock(RequestInterface::class));

        $this->assertArrayHasKey('foo', $searchParams);
        $this->assertArrayHasKey('foo', $searchParams['foo']);
        $this->assertCount(2, $searchParams['foo']['foo']);
        $this->assertArrayHasKey('bar', $searchParams['foo']);
        $this->assertEquals('bar', $searchParams['foo']['bar']);
        $this->assertArrayHasKey('baz', $searchParams['foo']);
        $this->assertEquals('baz', $searchParams['foo']['baz']);

        $this->assertArrayHasKey('bar', $searchParams);
        $this->assertCount(2, $searchParams['bar']);

        $this->assertArrayHasKey('baz', $searchParams);
        $this->assertEquals('baz', $searchParams['baz']);

        $this->assertArrayHasKey('qux', $searchParams);
        $this->assertEquals('qux', $searchParams['qux']);

        $this->assertArrayHasKey('empty', $searchParams);
        $this->assertEmpty($searchParams['empty']);

        $this->assertArrayHasKey('null', $searchParams);
        $this->assertNull($searchParams['null']);

        $this->assertArrayHasKey('false', $searchParams);
        $this->assertFalse($searchParams['false']);

        $this->assertArrayHasKey('zero', $searchParams);
        $this->assertEquals(0, $searchParams['zero']);

        $this->assertArrayHasKey('emptystring', $searchParams);
        $this->assertEquals("", $searchParams['emptystring']);
    }


    /**
     * Init search params provider used during tests.
     *
     * @return SearchParamsProvider
     */
    private function getProvider($rescorer = null)
    {
        if ($rescorer == null) {
            $rescorer = $this->createMock(RescorerInterface::class);
            $rescorer->expects($this->once())->method('prepareSearchParams')->will($this->returnArgument(1));
        }

        $rescorerResolver = $this->createMock(RescorerResolverInterface::class);
        $rescorerResolver->expects($this->once())->method('getRescorer')->willReturn($rescorer);

        $providers = array_map([$this, 'createProviderMock'], $this->providersData);

        return new SearchParamsProvider($rescorerResolver, $providers);
    }

    /**
     * Configure a search params provider from mocked data.
     *
     * @param array $data
     *
     * @return SearchParamsProviderInterface
     */
    private function createProviderMock($data)
    {
        $provider = $this->createMock(SearchParamsProviderInterface::class);
        $provider->method('getParams')->willReturn($data);

        return $provider;
    }
}
