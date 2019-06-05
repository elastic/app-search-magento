<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\SearchAdapter;

use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;

/**
 * AppSearch search adapter implementation.
 *
 * @package   Elastic\AppSearch\CatalogSearch\SearchAdapter
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
     * @var ResponseBuilder
     */
    private $responseBuilder;

    /**
     * Constructor.
     *
     * @param RequestExecutor $requestExcecutor
     * @param ResponseBuilder $responseBuilder
     */
    public function __construct(RequestExecutor $requestExecutor, ResponseBuilder $responseBuilder)
    {
        $this->requestExecutor = $requestExecutor;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function query(RequestInterface $request)
    {
        return $this->responseBuilder->buildResponse($this->requestExecutor->execute($request));
    }
}
