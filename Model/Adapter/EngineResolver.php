<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter;

use Elastic\AppSearch\Model\Adapter\Engine\EngineNameResolver;
use Elastic\AppSearch\Model\Adapter\Engine\LanguageResolver;

/**
 * Engine resolver implementation.
 *
 * @package   Elastic\Model\Adapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineResolver implements EngineResolverInterface
{
    /**
     * @var EngineInterface[]
     */
    private $engines = [];

    /**
     * @var EngineNameResolver
     */
    private $engineNameResolver;

    /**
     * @var LanguageResolver
     */
    private $languageResolver;

    /**
     * @var EngineInterfaceFactory
     */
    private $engineFactory;

    /**
     * Constructor.
     *
     * @param EngineInterfaceFactory $engineFactory
     * @param EngineNameResolver     $engineNameResolver
     * @param LanguageResolver       $languageResolver
     */
    public function __construct(
        EngineInterfaceFactory $engineFactory,
        EngineNameResolver $engineNameResolver,
        LanguageResolver $languageResolver
    ) {
        $this->engineNameResolver = $engineNameResolver;
        $this->languageResolver   = $languageResolver;
        $this->engineFactory      = $engineFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getEngine(string $engineIdentifier, int $storeId): EngineInterface
    {
        $engineKey = $engineIdentifier . '_' . $storeId;

        if (!isset($this->engines[$engineKey])) {
            $this->engines[$engineKey] = $this->initEngine($engineIdentifier, $storeId);
        }

        return $this->engines[$engineKey];
    }

    /**
     * Create a new engine from config.
     *
     * @param string $engineIdentifier
     * @param int    $storeId
     *
     * @return EngineInterface
     */
    private function initEngine(string $engineIdentifier, int $storeId): EngineInterface
    {
        $engineParams = [
            'identifier' => $engineIdentifier,
            'storeId'    => $storeId,
            'name'       => $this->engineNameResolver->getEngineName($engineIdentifier, $storeId),
            'language'   => $this->languageResolver->getLanguage($storeId),
        ];

        return $this->engineFactory->create($engineParams);
    }
}
