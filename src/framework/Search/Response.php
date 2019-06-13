<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Search;

use Magento\Framework\Search\Response\QueryResponse;
use Magento\Framework\Api\Search\AggregationInterface;

/**
 * AppSearch search adapter response implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Response extends QueryResponse
{
    /**
     * @var int
     */
    private $count;

    /**
     * Constructor.
     *
     * @param array                $documents
     * @param AggregationInterface $aggregations
     * @param int                  $count
     */
    public function __construct(array $documents, AggregationInterface $aggregations, int $count)
    {
        parent::__construct($documents, $aggregations);
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->count;
    }
}
