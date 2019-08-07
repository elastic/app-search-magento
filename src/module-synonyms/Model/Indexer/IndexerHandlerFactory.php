<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Synonyms\Model\Indexer;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\EngineResolverInterface;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;

/**
 * Factory to create synonym save index handler.
 *
 * @package   Elastic\AppSearch\Synonyms\Model\Indexer
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class IndexerHandlerFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * @var string[]
     */
    private $handlers = null;

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface  $objectManager
     * @param EngineResolverInterface $engineResolver
     * @param string[]                $handlers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        EngineResolverInterface $engineResolver,
        array $handlers = []
    ) {
        $this->objectManager = $objectManager;
        $this->handlers = $handlers;
        $this->engineResolver = $engineResolver;
    }

    /**
     * Create indexer handler
     *
     * @SuppressWarnings(PHPMD.MissingImport)
     *
     * @param array $data
     *
     * @return IndexerInterface
     */
    public function create(array $data = []): ?IndexerInterface
    {
        $indexer = null;

        $searchEngine = $this->engineResolver->getCurrentSearchEngine();

        if (isset($this->handlers[$searchEngine])) {
            $indexer = $this->objectManager->create($this->handlers[$searchEngine], $data);

            if (!$indexer instanceof IndexerInterface) {
                $message = $searchEngine . ' indexer handler doesn\'t implement ' . IndexerInterface::class;
                throw new \InvalidArgumentException($message);
            }

            if ($indexer && !$indexer->isAvailable()) {
                throw new \LogicException('Indexer handler is not available: ' . $searchEngine);
            }
        }

        return $indexer;
    }
}
