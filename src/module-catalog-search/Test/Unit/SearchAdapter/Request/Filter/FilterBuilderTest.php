<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Request\Filter;

use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\FilterBuilder;
use Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\FilterBuilderInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\FieldMapperInterface;
use Magento\Framework\Validator\UniversalFactory;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\SearchAdapter\Request\Filter\FilterBuilder class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Request\Filter
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class FilterBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Check filter content when using a valid filter.
     */
    public function testBuildValidFilter()
    {
        $builder = $this->getFilterBuilder();

        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getType')->willReturn('myFilterType');

        $this->assertEquals(["filterContent"], $builder->getFilter($filter));
    }

    /**
     * Check an exception is thrown when using an invalid filter.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testBuildInvalidFilter()
    {
        $builder = $this->getFilterBuilder();

        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getType')->willReturn('unknownFilterType');

        $builder->getFilter($filter);
    }

    /**
     * Create the builder used in tests.
     *
     * @return FilterBuilder
     */
    private function getFilterBuilder()
    {
        $builder = $this->createMock(FilterBuilderInterface::class);
        $builder->method('getFilter')->willReturn(['filterContent']);

        $builderFactory = $this->createMock(UniversalFactory::class);
        $builderFactory->method('create')->willReturn($builder);

        $fieldMapper = $this->createMock(FieldMapperInterface::class);

        return new FilterBuilder($fieldMapper, ['myFilterType' => $builderFactory]);
    }
}