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
 * Rescore a query result set.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface RescorerInterface
{
    /**
     * @var int
     */
    const MAX_SIZE = 100;

    /**
     * Prepare search params for rescoring.
     *
     * @param RequestInterface $request
     * @param string           $queryText
     * @param array            $searchParams
     *
     * @return array
     */
    public function prepareSearchParams(RequestInterface $request, string $queryText, array $searchParams): array;

    /**
     * Apply rescoring to the result set.
     *
     * @param RequestInterface $request
     * @param array            $results
     *
     * @return array
     */
    public function rescoreResults(RequestInterface $request, array $results): array;
}
