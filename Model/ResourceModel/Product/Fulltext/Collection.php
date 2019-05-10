<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\ResourceModel\Product\Fulltext;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\EngineResolverInterface;

/**
 * AppSearch search product collection.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 *
 * @package   Elastic\AppSearch\Model\ResourceModel\Product\Fulltext
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    /**
     * @var string||NULL
     */
    private $relevanceOrderDirection;

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        if (!$this->isAppSearch()) {
            return parent::getSize();
        }

        if ($this->_totalRecords === null) {
            $this->_totalRecords = current($this->getFacetedData('_meta'))['count'];
        }

        return $this->_totalRecords;
    }

    /**
     * {@inheritDoc}
     */
    public function setOrder($attribute, $dir = Select::SQL_DESC)
    {
        if ($this->isAppSearch()) {
            if ($attribute === 'price') {
                $this->_orders[$attribute] = $dir;
                return $this;
            }

            if ($attribute === 'relevance') {
                $this->relevanceOrderDirection = $dir;
            }
        }

        return parent::setOrder($attribute, $dir);
    }

    /**
     * {@inheritDoc}
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($this->isAppSearch()) {
            if ($attribute == 'position') {
                $dir = strtoupper($dir) == self::SORT_ORDER_ASC ? self::SORT_ORDER_DESC : self::SORT_ORDER_ASC;
                return $this->setOrder('relevance', $dir);
            }
        }

        return parent::addAttributeToSort($attribute, $dir);
    }

    /**
     * {@inheritDoc}
     */
    public function getFacetedData($field)
    {
        try {
            return parent::getFacetedData($field);
        } catch (\Magento\Framework\Exception\StateException $e) {
            return [];
        }
    }

    // phpcs:disable
    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * {@inheritDoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->isAppSearch()) {
            $searchCriteriaBuilder = ObjectManager::getInstance()->get(SearchCriteriaBuilder::class);

            if ($this->relevanceOrderDirection) {
                $searchCriteriaBuilder->addSortOrder('_score', $this->relevanceOrderDirection);
            } elseif ($this->_orders) {
                $searchCriteriaBuilder->addSortOrder(current(array_keys($this->_orders)), current($this->_orders));
            }

            if ($this->getPageSize()) {
                $searchCriteriaBuilder->setCurrentPage(max(1, round((int) $this->_curPage)));
                $searchCriteriaBuilder->setPageSize($this->_pageSize);
                $this->_pageSize = null;
            }
        }

        return parent::_renderFiltersBefore();
    }

    /**
     * {@inheritDoc}
     */
    protected function _beforeLoad()
    {
        if ($this->isAppSearch() && empty($this->_orders) && !$this->relevanceOrderDirection) {
            $this->relevanceOrderDirection = self::SORT_ORDER_DESC;
        }

        return parent::_beforeLoad();
    }
    // phpcs:enable

    /**
     * Check if the currrent engine is App Search.
     *
     * @return bool
     */
    private function isAppSearch(): bool
    {
        return $this->getCurrentSearchEngine() === "elastic_appsearch";
    }

    /**
     * Return current engine name.
     *
     * @return string
     */
    private function getCurrentSearchEngine(): string
    {
        return ObjectManager::getInstance()->get(EngineResolverInterface::class)->getCurrentSearchEngine();
    }
}
