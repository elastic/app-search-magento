<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request;

use Magento\Framework\Search\RequestInterface;

/**
 * Retrieve rescorer for a request.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface RescorerResolverInterface
{
    /**
     * Get rescorer for a search request.
     *
     * @param RequestInterface $request
     *
     * @return RescorerInterface
     */
    public function getRescorer(RequestInterface $request): RescorerInterface;
}
