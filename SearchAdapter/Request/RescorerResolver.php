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
 * Rescorer resolver implementation.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class RescorerResolver implements RescorerResolverInterface
{
    /**
     * @var RescorerInterface[]
     */
    private $rescorers;

    /**
     * Constructor.
     *
     * @param RescorerInterface[] $rescorers
     */
    public function __construct(array $rescorers)
    {
        $this->rescorers = $rescorers;
    }

    /**
     * {@inheritDoc}
     */
    public function getRescorer(RequestInterface $request): RescorerInterface
    {
        return $this->rescorers[$request->getName()] ?? $this->rescorers['default'];
    }
}
