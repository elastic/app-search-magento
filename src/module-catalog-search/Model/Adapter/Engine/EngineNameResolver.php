<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filter\FilterManager;

/**
 * Connvert engine identifier into real App Search engine names.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineNameResolver
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var FilterManager
     */
    private $filter;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig Store configuration.
     * @param FilterManager        $filter      Filter manager.
     */
    public function __construct(ScopeConfigInterface $scopeConfig, FilterManager $filter)
    {
        $this->scopeConfig = $scopeConfig;
        $this->filter      = $filter;
    }

    /**
     * Return real App Search engine name.
     *
     * @param string $engineIdentifier Engine identifier (e.g. catalogsearch_fulltext)
     * @param int    $storeId          Store id.
     *
     * @return string
     */
    public function getEngineName(string $engineIdentifier, int $storeId): string
    {
        $enginePrefix = (string) $this->scopeConfig->getValue('elastic_appsearch/client/engine_prefix');
        $engineName   = sprintf('%s-%s-%s', $enginePrefix, $engineIdentifier, $storeId);

        return $this->filter->translitUrl($engineName);
    }
}
