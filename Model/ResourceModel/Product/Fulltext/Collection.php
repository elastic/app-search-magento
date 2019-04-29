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

/**
 * AppSearch search product collection.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 *
 * @package   Elastic\AppSearch\Model
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        if ($this->_totalRecords === null) {
            $this->_totalRecords = current($this->getFacetedData('_meta'))['count'];
        }
        return $this->_totalRecords;
    }

    // phpcs:disable
    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * {@inheritDoc}
     */
    protected function _renderFiltersBefore()
    {
        $searchCriteriaBuilder = ObjectManager::getInstance()->get(SearchCriteriaBuilder::class);

        if ($this->_orders) {
            $searchCriteriaBuilder->addSortOrder(current(array_keys($this->_orders)), current($this->_orders));
        }

        if ($this->getPageSize()) {
            $searchCriteriaBuilder->setCurrentPage(max(0, round((int) $this->_curPage - 1)));
            $searchCriteriaBuilder->setPageSize($this->_pageSize);
            $this->_pageSize = null;
        }

        return parent::_renderFiltersBefore();
    }

    /**
     * {@inheritDoc}
     */
    protected function _beforeLoad()
    {
        if (empty($this->_orders)) {
            $this->setOrder('_score', Select::SQL_DESC);
        }
        return parent::_beforeLoad();
    }
    // phpcs:enable
}
