<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\LanguageResolver;

/**
 * Unit test for the LanguageResolver class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class LanguageResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test expected language mapping.
     *
     * @param string $magentoLanguage
     * @param string $appSearchLanguage
     *
     * @testWith ["en_GB", "en"]
     *           ["en", "en"]
     *           ["sv_SE", null]
     *           ["sv", null]
     */
    public function testLanguage($magentoLanguage, $appSearchLanguage)
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $scopeConfig->expects($this->any())->method('getValue')->willReturn($magentoLanguage);

        $languageResolver = new LanguageResolver($scopeConfig);

        $this->assertEquals($appSearchLanguage, $languageResolver->getLanguage(0));
    }
}
