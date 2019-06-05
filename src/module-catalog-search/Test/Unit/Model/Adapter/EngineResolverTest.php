<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Adapter;

use Elastic\AppSearch\CatalogSearch\Model\Adapter\EngineResolver;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\EngineInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\EngineNameResolver;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\LanguageResolver;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\Model\Adapter\EngineResolver class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test getting an engine though the resolver.
     *
     * @return void
     */
    public function testGetEngine()
    {
        $engine = $this->createMock(EngineInterface::class);

        $engineFactory = $this->createMock('Elastic\AppSearch\CatalogSearch\Model\Adapter\EngineInterfaceFactory');
        $engineFactory->expects($this->any())->method('create')->willReturn($engine);

        $engineNameResolver = $this->createMock(EngineNameResolver::class);
        $languageResolver = $this->createMock(LanguageResolver::class);

        $engineResolver = new EngineResolver($engineFactory, $engineNameResolver, $languageResolver);

        $result = $engineResolver->getEngine('engine_identifier', 1);

        $this->assertInstanceOf(EngineInterface::class, $result);
        $this->assertEquals($engine, $result);
    }
}
