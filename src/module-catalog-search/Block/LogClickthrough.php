<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Elastic\AppSearch\Framework\AppSearch\EngineResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Client\ClientConfigurationInterface;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\CatalogSearch\Block\SearchTermsLog;
use Elastic\AppSearch\CatalogSearch\Model\Config as AppSearchConfig;

/**
 * Block used to add JS clicktrough tracking to a search result page.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Block
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class LogClickthrough extends Template
{
    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * @var ClientConfigurationInterface
     */
    private $clientConfiguration;

    /**
     * @var SearchHelper
     */
    private $searchHelper;

    /**
     * @var SearchTermsLog
     */
    private $searchTermsLog;

    /**
     * @var AppSearchConfig
     */
    private $appSearchConfig;

    /**
     * Constructor.
     *
     * @param Context                      $context
     * @param ClientConfigurationInterface $clientConfiguration
     * @param EngineResolverInterface      $engineResolver
     * @param SearchHelper                 $searchHelper
     * @param SearchTermsLog               $searchTermsLog
     * @param AppSearchConfig              $appSearchConfig
     * @param array                        $data
     */
    public function __construct(
        Context $context,
        ClientConfigurationInterface $clientConfiguration,
        EngineResolverInterface $engineResolver,
        SearchHelper $searchHelper,
        SearchTermsLog $searchTermsLog,
        AppSearchConfig $appSearchConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->engineResolver      = $engineResolver;
        $this->clientConfiguration = $clientConfiguration;
        $this->searchHelper        = $searchHelper;
        $this->searchTermsLog      = $searchTermsLog;
        $this->appSearchConfig     = $appSearchConfig;
    }

    /**
     * Return App Search engine name to use for log clicks.
     *
     * @return string
     */
    public function getEngineName(): string
    {
        $store  = $this->_storeManager->getStore();
        $engine = $this->engineResolver->getEngine($this->getEngineIdentifier(), $store->getId());

        return $engine->getName();
    }

    /**
     * App Search API endpoint.
     *
     * @return string
     */
    public function getApiEndpoint(): ?string
    {
        $urlComponents = parse_url($this->clientConfiguration->getApiEndpoint());
        $scheme = !empty($urlComponents['scheme']) ? $urlComponents['scheme'] . ':' : '';
        $port   = !empty($urlComponents['port']) ? ':' . $urlComponents['port'] : '';

        return sprintf("%s//%s%s", $scheme, $urlComponents['host'], $port);
    }

    /**
     * Return App Search public search API key.
     *
     * @return string
     */
    public function getApiKey(): ?string
    {
        return $this->clientConfiguration->getSearchApiKey();
    }

    /**
     * Current search query.
     *
     * @return string
     */
    public function getQueryText(): string
    {
        return $this->searchHelper->getEscapedQueryText();
    }

    /**
     * Indicate if the page will be cached and if we should run a search query using the browser
     * in order to have consistent analytics.
     *
     * @return bool
     */
    public function doSearch(): bool
    {
        return $this->searchTermsLog->isPageCacheable();
    }

    /**
     * Indicate if tracking should be rendered or not.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isAppSearch() && $this->getApiKey() && $this->getApiEndpoint();
    }

    /**
     * Indicate if the current engine is App Search or not.
     *
     * @return bool
     */
    private function isAppSearch(): bool
    {
        return $this->appSearchConfig->isAppSearchEnabled();
    }
}
