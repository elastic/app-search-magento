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
use Elastic\AppSearch\CatalogSearch\Model\Config;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider as AttributeProvider;

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
     * @var int
     */
    const DEFAULT_WEIGHT = 1;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AttributeProvider
     */
    private $attributeProvider;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config, AttributeProvider $attributeProvider)
    {
        $this->config            = $config;
        $this->attributeProvider = $attributeProvider;
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
        if (!$this->config->isAppSearchEnabled()) {
            return $result;
        }

        if ($this->config->isCategoryNameSearchEnabled()) {
            $result['quick_search_container']['queries']['search']['match'][] = [
                'field' => $this->config->getCategoryNameField(),
                'boost' => $this->config->getCategoryNameWeight() ?? self::DEFAULT_WEIGHT,
            ];
        }

        $skuAttribute = $this->attributeProvider->getSearchableAttribute(ProductInterface::SKU);

        if ($skuAttribute && $skuAttribute->getIsSearchable()) {
            $result['quick_search_container']['queries']['search']['match'][] = [
                'field' => ProductInterface::SKU,
                'boost' => floatval($skuAttribute->getSearchWeight() ?? self::DEFAULT_WEIGHT)
            ];
        }

        return $result;
    }
}
