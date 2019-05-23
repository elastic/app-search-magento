<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Elastic\AppSearch\Model\Adapter\EngineResolverInterface;
use Elastic\AppSearch\Client\ClientConfigurationInterface;
use Magento\Search\Helper\Data as SearchHelper;

/**
 * Block used to add JS clicktrough tracking to a search result page.
 *
 * @package   Elastic\AppSearch\Block
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
     * Constructor.
     *
     * @param Context                      $context
     * @param ClientConfigurationInterface $clientConfiguration
     * @param EngineResolverInterface      $engineResolver
     * @param SearchHelper                 $searchHelper
     * @param array                        $data
     */
    public function __construct(
        Context $context,
        ClientConfigurationInterface $clientConfiguration,
        EngineResolverInterface $engineResolver,
        SearchHelper $searchHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->engineResolver      = $engineResolver;
        $this->clientConfiguration = $clientConfiguration;
        $this->searchHelper        = $searchHelper;
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
    public function getApiEndpoint(): string
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
    public function getApiKey(): string
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
}
