<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Search\Request;

use Magento\Framework\App\ObjectManager;
use Elastic\AppSearch\Search\Request;

/**
 * AppSearch search request builder: append sort support to the original builder.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\Search\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Builder extends \Magento\Framework\Search\Request\Builder
{
    /**
     * @var \Magento\Framework\Api\SortOrder[]
     */
    private $sort;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * {@inheritDoc}
     */
    public function create()
    {
        $request       = parent::create();
        $objectManager = ObjectManager::getInstance();

        $requestData = [
            'name'       => $request->getName(),
            'indexName'  => $request->getIndex(),
            'query'      => $request->getQuery(),
            'from'       => $request->getFrom(),
            'size'       => $request->getSize(),
            'dimensions' => $request->getDimensions(),
            'buckets'    => $request->getAggregation(),
        ];

        if (!empty($this->sort)) {
            $requestData['sort'] = $this->sort;
        }

        return $objectManager->create(Request::class, $requestData);
    }

    /**
    * Set sort order for the request.
    *
    * @param \Magento\Framework\Api\SortOrder[] $sort
    *
    * @return $this
    */
    public function setSort($sort)
    {
        $this->sort = array_filter($sort, function ($sortOrder) {
            return $sortOrder->getField() && $sortOrder->getDirection();
        });

        return $this;
    }
}
