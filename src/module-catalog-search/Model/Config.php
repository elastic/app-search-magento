<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model;

use Magento\Framework\Search\EngineResolverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;


/**
 * App Search catalog search config.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Config
{
    /**
     * @var string
     */
    const ENGINE_NAME = 'elastic_appsearch';

    /**
     * @var string
     */
    const CATEGORY_NAME_FIELD = 'category_name';

    /**
     * @var string
     */
    const CATEGORY_NAME_ENABLED_PATH = 'catalog/search/category_name_weight';

    /**
     * @var string
     */
    const CATEGORY_WEIGHT_PATH = 'catalog/search/category_name_weight';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * Constructor.
     *
     * @param EngineResolverInterface $engineResolver
     */
    public function __construct(EngineResolverInterface $engineResolver, ScopeConfigInterface $scopeConfig)
    {
        $this->engineResolver = $engineResolver;
        $this->scopeConfig    = $scopeConfig;
    }

    /**
     * Indicates if the currently configured engine is App Search
     *
     * @return bool
     */
    public function isAppSearchEnabled(): bool
    {
        return $this->engineResolver->getCurrentSearchEngine() === self::ENGINE_NAME;
    }

    /**
     * Indicate if search is search in category names is enable.
     *
     * @return boolean
     */
    public function isCategoryNameSearchEnabled(): bool
    {
        return (bool) $this->scopeConfig->isSetFlag(self::CATEGORY_NAME_ENABLED_PATH);
    }

    /**
     * Category name search weight.
     *
     * @return int
     */
    public function getCategoryNameWeight(): int
    {
        return (int) $this->scopeConfig->getValue(self::CATEGORY_WEIGHT_PATH);
    }

    /**
     * Field name used to match category name.
     *
     * @return string
     */
    public function getCategoryNameField(): string
    {
        return self::CATEGORY_NAME_FIELD;
    }
}
