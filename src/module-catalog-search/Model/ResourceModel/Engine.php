<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\ResourceModel;

use Magento\CatalogSearch\Model\ResourceModel\EngineInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;

/**
 * AppSearch search engine resource model.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model\ResourceModel
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Engine implements EngineInterface
{
    /**
     * Catalog product visibility
     *
     * @var Visibility
     */
    private $catalogProductVisibility;

    /**
     * @var IndexScopeResolver
     */
    private $indexScopeResolver;

    /**
     * Constructor.
     *
     * @param Visibility         $catalogProductVisibility
     * @param IndexScopeResolver $indexScopeResolver
     */
    public function __construct(Visibility $catalogProductVisibility, IndexScopeResolver $indexScopeResolver)
    {
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->indexScopeResolver = $indexScopeResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedVisibility()
    {
        return $this->catalogProductVisibility->getVisibleInSiteIds();
    }

    /**
     *
     * {@inheritDoc}
     */
    public function allowAdvancedIndex()
    {
        return false;
    }
    /**
     * {@inheritdoc}
     */
    public function processAttributeValue($attribute, $value)
    {
        return $value;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * {@inheritDoc}
     */
    public function prepareEntityIndex($index, $separator = ' ')
    {
        return $index;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;
    }
}
