<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Page;

use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;

/**
 * Pagination search params.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Page
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var int
     */
    const MAX_PAGE_SIZE = 100;

    /**
     * @var string
     */
    const PAGE_PARAM_NAME = 'page';

    /**
     * @var string
     */
    const CURRENT_PAGE_PARAM_NAME = 'current';

    /**
     * @var string
     */
    const PAGE_SIZE_PARAM_NAME = 'size';

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $pageParams = [
            self::CURRENT_PAGE_PARAM_NAME => $this->getCurrentPage($request),
            self::PAGE_SIZE_PARAM_NAME    => $this->getPageSize($request),
        ];

        return [self::PAGE_PARAM_NAME => $pageParams];
    }

    /**
     * Return page size for the request.
     *
     * @param RequestInterface $request
     *
     * @return int
     */
    private function getPageSize(RequestInterface $request)
    {
        return (int) min(self::MAX_PAGE_SIZE, $request->getSize() ?? self::MAX_PAGE_SIZE);
    }

    /**
     * Return current page for the request.
     *
     * @param RequestInterface $request
     *
     * @return int
     */
    private function getCurrentPage(RequestInterface $request)
    {
        return (int) floor(($request->getFrom() ?? 0) / max(1, $this->getPageSize($request))) + 1;
    }
}
