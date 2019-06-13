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

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\EngineResolver;

/**
 * Unit test for the EngineResolver class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\SearchAdapter\RequestExecutor
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class EnngineResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test resolve engine from a search request.
     */
    public function testGetEngine()
    {
        $dimension = $this->createMock(\Magento\Framework\Search\Request\Dimension::class);
        $dimension->method('getValue')->willReturn(1);

        $request = $this->createMock(\Magento\Framework\Search\RequestInterface::class);
        $request->method('getDimensions')->willReturn([$dimension]);
        $request->method('getIndex')->willReturn('index');

        $engine = $this->getEngineResolver()->getEngine($request);

        $this->assertInstanceOf(\Elastic\AppSearch\Framework\AppSearch\EngineInterface::class, $engine);
        $this->assertEquals('index_1', $engine->getName());
    }

    /**
     * Engine resolver used during tests.
     *
     * @return EngineResolver
     */
    private function getEngineResolver()
    {
        $engineResolver = $this->createMock(\Elastic\AppSearch\Framework\AppSearch\EngineResolverInterface::class);
        $engineResolver->method('getEngine')->willReturnCallback(
            function ($engineName, $scopeId) {
                $engine = $this->createMock(\Elastic\AppSearch\Framework\AppSearch\EngineInterface::class);
                $engine->method('getName')->willReturnCallback(function () use ($engineName, $scopeId) {
                    return $engineName . '_' . $scopeId;
                });
                return $engine;
            }
        );
        $scopeResolver = $this->createMock(\Magento\Framework\App\ScopeResolverInterface::class);
        $scopeResolver->method('getScope')->willReturnCallback(function ($scopeId) {
            $scope = $this->createMock(\Magento\Framework\App\ScopeInterface::class);
            $scope->method('getId')->willReturn($scopeId);

            return $scope;
        });

        return new EngineResolver($engineResolver, $scopeResolver);
    }
}
