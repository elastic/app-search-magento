<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit;

use Elastic\AppSearch\Framework\AppSearch\EngineResolver;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\EngineNameResolver;
use Elastic\AppSearch\Framework\AppSearch\Engine\LanguageResolver;

/**
 * Unit test for the EngineResolver class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit
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

        $engineFactory = $this->createMock('Elastic\AppSearch\Framework\AppSearch\EngineInterfaceFactory');
        $engineFactory->expects($this->any())->method('create')->willReturn($engine);

        $engineNameResolver = $this->createMock(EngineNameResolver::class);
        $languageResolver = $this->createMock(LanguageResolver::class);

        $engineResolver = new EngineResolver($engineFactory, $engineNameResolver, $languageResolver);

        $result = $engineResolver->getEngine('engine_identifier', 1);

        $this->assertInstanceOf(EngineInterface::class, $result);
        $this->assertEquals($engine, $result);
    }
}
