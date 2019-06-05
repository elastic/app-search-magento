<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter;

/**
 * App Search Engine implementation.
 *
 * @package   Elastic\Model\Adapter
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Engine implements EngineInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|NULL
     */
    private $language;

    /**
     * Constructor.
     *
     * @param string      $identifier Engine Magento identifier (e.g. catalogsearch_fulltext).
     * @param int         $storeId    Engine store id.
     * @param string      $name       Real App Search engine name (e.g. magento2-catalogsearch-fulltext-1)
     * @param string|NULL $language   Engine language (null if universal).
     */
    public function __construct(string $identifier, int $storeId, string $name, ?string $language)
    {
        $this->identifier = $identifier;
        $this->storeId    = $storeId;
        $this->name       = $name;
        $this->language   = $language;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getStoreId(): int
    {
        return $this->storeId;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }
}
