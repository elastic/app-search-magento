<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Sort;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Sort\SearchParamsProvider;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Elastic\AppSearch\Framework\Search\Request;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SortOrder;

/**
 * Unit test for the SearchParamsProvider class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\RequestExecutor\Sort
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class SearchParamsProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Test sort order params generation.
     *
     * @dataProvider requestDataProvider
     */
    public function testGetSearchParams(RequestInterface $request, $expectedResult)
    {
        $params = $this->getSearchParamsProvider()->getParams($request);
        $this->assertEquals($expectedResult, $params);
    }

    /**
     * Build sample requests to be tested and expected result.
     *
     * @return array
     */
    public function requestDataProvider(): array
    {
        $objectManager = new ObjectManager($this);

        return [
          [
            $objectManager->getObject(Request::class),
            [],
          ],
          [
            $this->getRequest([$this->getSortOrder('foo', SortOrder::SORT_ASC)]),
            ['sort' => [['foo' => 'asc']]],
          ],
          [
            $this->getRequest([$this->getSortOrder('foo', SortOrder::SORT_DESC)]),
            ['sort' => [['foo' => 'desc']]],
          ],
          [
            $this->getRequest(
                [$this->getSortOrder('foo', SortOrder::SORT_ASC), $this->getSortOrder('bar', SortOrder::SORT_ASC)]
            ),
            ['sort' => [['foo' => 'asc'], ['bar' => 'asc']]],
          ],
        ];
    }

    /**
     * Search params providers used during the tests.
     * @return
     */
    private function getSearchParamsProvider(): SearchParamsProvider
    {
        $fieldMapper = $this->createMock(FieldMapperInterface::class);
        $fieldMapper->method('getFieldName')->will($this->returnArgument(0));

        $fieldMapperResolver = $this->createMock(FieldMapperResolverInterface::class);
        $fieldMapperResolver->method('getFieldMapper')->willReturn($fieldMapper);

        return new SearchParamsProvider($fieldMapperResolver);
    }

    /**
     * Object manager used during tests.
     * @return ObjectManager
     */
    private function getObjectManager(): ObjectManager
    {
        if (!$this->objectManager) {
            $this->objectManager = new ObjectManager($this);
        }

        return $this->objectManager;
    }

    /**
     * Build a query with the specified sort orders.
     *
     * @return Request
     */
    private function getRequest(array $sortOrders): Request
    {
        return $this->getObjectManager()->getObject(Request::class, ['index' => 'index', 'sort' => $sortOrders]);
    }

    /**
     * Build a sort order from field & direction.
     *
     * @return SortOrder
     */
    private function getSortOrder($field, $direction): SortOrder
    {
        return new SortOrder([SortOrder::FIELD => $field, SortOrder::DIRECTION => $direction]);
    }
}
