<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model;

use Magento\Framework\Search\EngineResolverInterface;


/**
 * App Search catalog search config.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Model
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Config
{
    /**
     * @var string
     */
    const ENGINE_NAME = 'elastic_appsearch';

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * Constructor.
     *
     * @param EngineResolverInterface $engineResolver
     */
    public function __construct(EngineResolverInterface $engineResolver)
    {
        $this->engineResolver = $engineResolver;
    }

    /**
     * Indicates if the currently configured engine is App Search
     *
     * @return bool
     */
    public function isAppSearchEnabled(): bool
    {
        return $this->engineResolver->getCurrentSearchEngine() === self::ENGINE_NAME;
    }
}
