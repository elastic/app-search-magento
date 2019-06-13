<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Rescorer;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\ResponseProcessorInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\RescorerResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\RescorerInterface;

/**
 * Apply rescorers to the search response.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Rescorer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ResponseProcessor implements ResponseProcessorInterface
{
    /**
     * @var RescorerResolverInterface
     */
    private $rescorerResolver;

    /**
     * Constructor.
     *
     * @param RescorerResolverInterface $rescorerResolver
     */
    public function __construct(RescorerResolverInterface $rescorerResolver)
    {
        $this->rescorerResolver = $rescorerResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function process(RequestInterface $request, array $response): array
    {
        if (!isset($response['results'])) {
            $response['results'] = [];
        }

        if (!empty($response['results'])) {
            $response['results'] = $this->getRescorer($request)->rescoreResults($request, $response['results']);
        }

        return $response;
    }

    /**
     * Get rescorer for the current request.
     *
     * @param RequestInterface $request
     *
     * @return RescorerInterface
     */
    private function getRescorer(RequestInterface $request): RescorerInterface
    {
        return $this->rescorerResolver->getRescorer($request);
    }
}
