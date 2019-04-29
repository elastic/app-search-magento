<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Search;

use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\Request\QueryInterface;

/**
 * AppSearch search request append sort support to the original request.
 *
 * @deprecated Will be removed when dropping compat. with Magento < 2.4.x.
 *
 * @package   Elastic\AppSearch\Search
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Request extends \Magento\Framework\Search\Request
{
    /**
     * @var \Magento\Framework\Api\SortOrder[]
     */
    private $sort;

    /**
     * @param string $name
     * @param string $indexName
     * @param QueryInterface $query
     * @param int|null $from
     * @param int|null $size
     * @param Dimension[] $dimensions
     * @param RequestBucketInterface[] $buckets
     * @param array $sort
     */
    public function __construct(
        $name,
        $indexName,
        QueryInterface $query,
        $from = null,
        $size = null,
        array $dimensions = [],
        array $buckets = [],
        $sort = []
    ) {
        parent::__construct($name, $indexName, $query, $from, $size, $dimensions, $buckets);
        $this->sort = $sort;
    }

    /**
     * Request sort orders.
     *
     * @return \Magento\Framework\Api\SortOrder[]
     */
    public function getSort()
    {
        return $this->sort;
    }
}
