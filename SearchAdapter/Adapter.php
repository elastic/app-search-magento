<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter;

use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;

/**
 * AppSearch search adapter implementation.
 *
 * @package   Elastic\Model\SearchAdapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Adapter implements AdapterInterface
{
    /**
     * @var RequestExecutor
     */
    private $requestExecutor;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * Constructor.
     *
     * @param RequestExecutor $requestExcecutor
     * @param ResponseFactory $responseFactory
     */
    public function __construct(RequestExecutor $requestExecutor, ResponseFactory $responseFactory)
    {
        $this->requestExecutor = $requestExecutor;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function query(RequestInterface $request)
    {
        return $this->responseFactory->create($this->requestExecutor->execute($request));
    }
}
