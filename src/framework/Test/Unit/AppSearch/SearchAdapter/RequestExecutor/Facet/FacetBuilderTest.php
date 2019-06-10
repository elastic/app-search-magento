<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Facet;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Facet\FacetBuilder;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Facet\DynamicRangeProvider;
use Magento\Framework\Search\Request\Aggregation\TermBucket;
use Magento\Framework\Search\Request\Aggregation\Range;
use Magento\Framework\Search\Request\Aggregation\RangeBucket;
use Magento\Framework\Search\Request\Aggregation\DynamicBucket;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Facet\FacetBuilderTest class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor\Facet
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class FacetBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Build a value facet from a term bucket object.
     */
    public function testBuildTermBucket()
    {
        $facets = $this->getBuilder()->getFacet(new TermBucket('my_bucket', 'my_field', []));

        $this->assertArrayHasKey('my_field', $facets);
        $this->assertCount(1, $facets['my_field']);
        $this->assertEquals('my_bucket', current($facets['my_field'])['name']);
        $this->assertEquals('value', current($facets['my_field'])['type']);
        $this->assertEquals(250, current($facets['my_field'])['size']);
    }

    /**
     * Build a range facet from a range bucket object.
     */
    public function testRangeTermBucket()
    {
        $ranges = [new Range(0, 100), new Range(100, 200)];
        $facets = $this->getBuilder()->getFacet(new RangeBucket('my_bucket', 'my_field', [], $ranges));

        $this->assertArrayHasKey('my_field', $facets);
        $this->assertCount(1, $facets['my_field']);
        $this->assertEquals('my_bucket', current($facets['my_field'])['name']);
        $this->assertCount(2, current($facets['my_field'])['ranges']);

        foreach (current($facets['my_field'])['ranges'] as $rangeIndex => $range) {
            $this->assertEquals($ranges[$rangeIndex]->getFrom(), $range['from']);
            $this->assertEquals($ranges[$rangeIndex]->getTo(), $range['to']);
        }
    }

    /**
     * Build a range facet from a dynamic bucket object.
     */
    public function testDynanicTermBucket()
    {
        $facets = $this->getBuilder()->getFacet(new DynamicBucket('my_bucket', 'my_field', 'method'));

        $this->assertArrayHasKey('my_field', $facets);
        $this->assertCount(1, $facets['my_field']);
        $this->assertEquals('my_bucket', current($facets['my_field'])['name']);
        $this->assertCount(2, current($facets['my_field'])['ranges']);

        foreach (current($facets['my_field'])['ranges'] as $rangeIndex => $range) {
            $this->assertEquals($rangeIndex * 1000, $range['from']);
            $this->assertEquals(($rangeIndex + 1) * 1000, $range['to']);
        }
    }

    /**
     * Builder used during tests.
     *
     * @return FacetBuilder
     */
    private function getBuilder()
    {
        $fieldMapper          = $this->createMock(FieldMapperInterface::class);
        $fieldMapper->method('getFieldName')->will($this->returnArgument(0));

        $dynamicRangeProvider = $this->createMock(DynamicRangeProvider::class);
        $dynamicRangeProvider->method('getRanges')->willReturn([new Range(0, 1000), new Range(1000, 2000)]);

        return new FacetBuilder($fieldMapper, $dynamicRangeProvider);
    }
}
