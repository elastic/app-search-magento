<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter\MatchQueryFilterBuilder;
use Magento\Framework\Search\Request\QueryInterface;

/**
 * Unit test for the MatchQueryFilterBuilder class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class MatchQueryFilterBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Always return an empty array since the match clause is not handled through filters,
     * but through the query App Search param.
     */
    public function testBuildValidQuery()
    {
        $query = $this->createMock(QueryInterface::class);
        $query->method('getName')->willReturn("search");

        $this->assertEquals([], $this->getQueryBuilder()->getFilter($query));
    }

    /**
     * An exception is thrown when trying to build a query that is different of the main search query.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testBuildInvalidQuery()
    {
        $query = $this->createMock(QueryInterface::class);
        $query->method('getName')->willReturn("not_search");

        $this->getQueryBuilder()->getFilter($query);
    }

    /**
     * Create the builder used in tests.
     *
     * @return MatchQueryFilterBuilder
     */
    private function getQueryBuilder()
    {
        return new MatchQueryFilterBuilder();
    }
}
