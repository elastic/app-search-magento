<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Request\Fulltext;

use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\QueryLocatorInterface;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Fulltext\SearchParamsProvider;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Fulltext\SearchParamsProvider class.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Request\Fulltext
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class SearchParamsProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $matchConfig = [
        ['field' => 'foo', 'boost' => 5],
        ['field' => 'bar', 'boost' => 1],
        ['field' => 'baz', 'boost' => 100],
        ['field' => 'qux'],
        ['field' => '*', 'boost' => 10],
    ];

    /**
     * Check params using self::matchConfig as configuration.
     */
    public function testSearchParams()
    {
        $searchParamsProvider = $this->getProvider();
        $request = $this->getRequestMock("search text", $this->matchConfig);

        $searchParams = $searchParamsProvider->getParams($request);

        $this->assertArrayHasKey('search_fields', $searchParams);
        $this->assertCount(3, $searchParams['search_fields']);

        $this->assertArrayHasKey('foo', $searchParams['search_fields']);
        $this->assertArrayHasKey('bar', $searchParams['search_fields']);
        $this->assertArrayHasKey('baz', $searchParams['search_fields']);

        $this->assertEquals(5, $searchParams['search_fields']['foo']['weight']);
        $this->assertEquals(1, $searchParams['search_fields']['bar']['weight']);
        $this->assertEquals(10, $searchParams['search_fields']['baz']['weight']);
    }

    /**
     * The search params should remains empty if the searched text is empty.
     *
     * @param NULL|string $value
     *
     * @testWith [null]
     *           [""]
     */
    public function testSearchParamsOnEmtpyText($value)
    {
        $searchParamsProvider = $this->getProvider();
        $request = $this->getRequestMock($value, $this->matchConfig);

        $this->assertEmpty($searchParamsProvider->getParams($request));
    }

    /**
     * The search params should remains empty if no query is return by the locator.
     */
    public function testSearchParamsOnNullQuery()
    {
        $searchParamsProvider = $this->getProvider();

        $request = $this->createMock(RequestInterface::class);
        $request->method('getQuery')->willReturn(null);

        $this->assertEmpty($searchParamsProvider->getParams($request));
    }

    /**
     * The search params should remains empty if no matches if specified in the query.
     *
     * @param array $matches
     *
     * @testWith [null]
     *           [[]]
     */
    public function testSearchParamsOnEmptyMatches($matches)
    {
        $searchParamsProvider = $this->getProvider();
        $request = $this->getRequestMock("text search", $matches);

        $this->assertEmpty($searchParamsProvider->getParams($request));
    }

    /**
     * Configure the fulltext search params provider used in tests.
     *
     * @return SearchParamsProvider
     */
    private function getProvider()
    {
        $queryLocator = $this->createMock(QueryLocatorInterface::class);
        $queryLocator->method('getQuery')->willReturnCallback(
            function ($request) {
                return $request->getQuery();
            }
        );

        $fieldMapper = $this->createMock(FieldMapperInterface::class);
        $fieldMapper->method('getFieldName')->will($this->returnArgument(0));


        $fieldMapperResolver = $this->createMock(FieldMapperResolverInterface::class);
        $fieldMapperResolver->method('getFieldMapper')->willReturn($fieldMapper);

        return new SearchParamsProvider($queryLocator, $fieldMapperResolver);
    }

    /**
     * Build a search request containg a search query.
     *
     * @param string $textValue
     * @param array  $matches
     *
     * @return RequestInterface
     */
    private function getRequestMock($textValue, $matches)
    {
        $methods = ['getType', 'getName', 'getBoost', 'getValue', 'getMatches'];

        $query = $this->createPartialMock(QueryInterface::class, $methods);
        $query->method('getValue')->willReturn($textValue);
        $query->method('getMatches')->willReturn($matches);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getQuery')->willReturn($query);
        $request->method('getIndex')->willReturn('catalogsearch_fulltext');

        return $request;
    }
}
