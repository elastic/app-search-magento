<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Response;

use Elastic\AppSearch\CatalogSearch\SearchAdapter\Response\DocumentCountResolver;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\SearchAdapter\Response\DocumentCountResolver class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\SearchAdapter\Response
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class DocumentCountResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the correct doc count is retrieved.
     */
    public function testGetDocumentCount()
    {
        $resolver = new DocumentCountResolver();
        $response = ['meta' => ['page' => ['total_results' => 10]]];
        $this->assertEquals(10, $resolver->getDocumentCount($response));
    }

    /**
     * Test 0 is returned when meta are missing into the response.
     */
    public function testEmptyResponse()
    {
        $resolver = new DocumentCountResolver();
        $response = [];
        $this->assertEquals(0, $resolver->getDocumentCount($response));
    }
}
