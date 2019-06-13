<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\GraphQl\Layer;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Catalog\Model\Category as Category;

/**
 * AppSearch search interface implementation. Append sort support.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * Constructor.
     *
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory            $collectionFactory
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get product collection for the current layer.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Category $category
     *
     * @return Collection
     */
    public function getCollection(Category $category) : Collection
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }
        return $this->collection;
    }

    /**
     * Set current search result (reset the product collection if needed).
     *
     * @param SearchResultInterface $searchResult
     *
     * @return $this
     */
    public function setSearchResult(SearchResultInterface $searchResult)
    {
        $this->collection = $this->collectionFactory->create();
        $this->collection->setSearchResult($searchResult);

        return $this;
    }
}
