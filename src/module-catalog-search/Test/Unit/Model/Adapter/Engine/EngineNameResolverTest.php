<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Adapter\Engine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\EngineNameResolver;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Filter\TranslitUrl;

/**
 * Unit test for the Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\EngineNameResolver class.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineNameResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test expected engine name for catalogsearch fulltext engine..
     */
    public function testLanguage()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->any())->method('getValue')->willReturn('engine_prefix');

        $engineNameResolver = new EngineNameResolver($scopeConfig, $this->getFilterManager());

        $engineName = $engineNameResolver->getEngineName('catalogsearch_fulltext', 1);

        $this->assertEquals('engine-prefix-catalogsearch-fulltext-1', $engineName);
    }

    /**
     * Init the filter manager used in test case.
     *
     * @return FilterManager
     */
    private function getFilterManager()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->any())->method('getValue')->willReturn(null);

        $urlTranslitFilter = new TranslitUrl($scopeConfig);

        $translitUrl = function ($value) use ($urlTranslitFilter) {
            return $urlTranslitFilter->filter($value);
        };

        $filterManager = $this->createPartialMock(FilterManager::class, ['translitUrl']);
        $filterManager->expects($this->any())->method('translitUrl')->willReturnCallback($translitUrl);

        return $filterManager;
    }
}
