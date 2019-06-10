<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Filter;

use Magento\Framework\Search\Request\QueryInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilterBuilderInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Filter\QueryFilterBuilder;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Magento\Framework\Validator\UniversalFactory;

/**
 * Unit test for the QueryFilterBuilder class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class QueryFilterBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Check filter content when using a valid filter.
     */
    public function testBuildValidFilter()
    {
        $builder = $this->getQueryFilterBuilder();

        $query = $this->createMock(QueryInterface::class);
        $query->method('getType')->willReturn('myQueryType');

        $this->assertEquals(["filterContent"], $builder->getFilter($query));
    }

    /**
     * Check an exception is thrown when using an invalid filter.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testBuildInvalidFilter()
    {
        $builder = $this->getQueryFilterBuilder();

        $query = $this->createMock(QueryInterface::class);
        $query->method('getType')->willReturn('unknownFilterType');

        $builder->getFilter($query);
    }

    /**
     * Create the builder used in tests.
     *
     * @return QueryFilterBuilder
     */
    private function getQueryFilterBuilder()
    {
        $builder = $this->createMock(QueryFilterBuilderInterface::class);
        $builder->method('getFilter')->willReturn(['filterContent']);

        $builderFactory = $this->createMock(UniversalFactory::class);
        $builderFactory->method('create')->willReturn($builder);

        $fieldMapper = $this->createMock(FieldMapperInterface::class);

        return new QueryFilterBuilder($fieldMapper, ['myQueryType' => $builderFactory]);
    }
}
