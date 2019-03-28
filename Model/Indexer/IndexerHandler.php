<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;

/**
 * Implementation of the App Search catalog product search index handler.
 *
 * @package   Elastic\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class IndexerHandler implements IndexerInterface
{
    /**
     * {@inheritDoc}
     */
    public function isAvailable($dimensions = [])
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function cleanIndex($dimensions)
    {
        return $this;
    }
}
