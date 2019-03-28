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

use Magento\Framework\Indexer\IndexStructureInterface;

/**
 * Implementation of the App Search catalog product search index handler.
 *
 * @package   Elastic\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class IndexStructure implements IndexStructureInterface
{
    /**
     * {@inheritDoc}
     */
    public function create($index, array $fields, array $dimensions = [])
    {

    }

    /**
     * {@inheritDoc}
     */
    public function delete($index, array $dimensions = [])
    {

    }
}
