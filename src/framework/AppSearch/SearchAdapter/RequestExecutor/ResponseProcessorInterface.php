<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor;

use Magento\Framework\Search\RequestInterface;

/**
 * Modify the search App Search response to make it more easy to consume by the response builder.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface ResponseProcessorInterface
{
    /**
     * Process response.
     *
     * @param RequestInterface $request
     * @param array            $response
     *
     * @return array
     */
    public function process(RequestInterface $request, array $response): array;
}
