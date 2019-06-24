<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Plugin\Search\Model;

use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Add category name to search fields if needed.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Plugin\Search\Model
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RequestGeneratorPlugin
{
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
     * @var int
     */
    const DEFAULT_WEIGHT = 1;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add category name to the search fields.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param RequestGenerator $requestGenerator
     * @param array            $result
     *
     * @return array
     */
    public function afterGenerate(RequestGenerator $requestGenerator, array $result)
    {
        if ($this->enableCategoryNameSearch()) {
            $result['quick_search_container']['queries']['search']['match'][] = [
                'field' => self::CATEGORY_NAME_FIELD,
                'boost' => $this->getCategoryNameWeight(),
            ];

            $result['quick_search_container']['queries']['search']['match'][] = [
                'field' => ProductInterface::SKU,
                'boost' => self::DEFAULT_WEIGHT
            ];
        }

        return $result;
    }

    /**
     * Indicate if search is search in category names is enable.
     *
     * @return boolean
     */
    private function enableCategoryNameSearch()
    {
        return $this->scopeConfig->isSetFlag(self::CATEGORY_NAME_ENABLED_PATH);
    }

    /**
     * Category name search weight.
     *
     * @return int
     */
    private function getCategoryNameWeight()
    {
        return (int) ($this->scopeConfig->getValue(self::CATEGORY_WEIGHT_PATH) ?? self::DEFAULT_WEIGHT);
    }
}
