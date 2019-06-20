<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Config\CacheInterface;

/**
 * Read field config from app_search_fields.xml file.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Config extends \Magento\Framework\Config\Data
{
    /**
     * Cache identifier
     */
    const CACHE_ID = 'app_search_fields_config';

    /**
     * Constructor.
     *
     * @param FilesystemReader         $reader
     * @param CacheInterface           $cache
     * @param string                   $cacheId
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        FilesystemReader $reader,
        CacheInterface $cache,
        string $cacheId = self::CACHE_ID,
        SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }
}
