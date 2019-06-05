<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine;

use Magento\Framework\Exception\LocalizedException;

/**
 * SchemaResolverInterface implementation.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SchemaResolver implements SchemaResolverInterface
{
    /**
     * @var SchemaInterface[]
     */
    private $schemas = [];

    /**
     * @var SchemaProviderInterface[]
     */
    private $providers;

    /**
     * Constructor.
     *
     * @param SchemaProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(string $engineIdentifier): SchemaInterface
    {
        if (!isset($this->schemas[$engineIdentifier]) && !isset($this->providers[$engineIdentifier])) {
            throw new LocalizedException(__('Could not localize schema for engine %1', $engineIdentifier));
        } elseif (!isset($this->schemas[$engineIdentifier])) {
            $this->schemas[$engineIdentifier] = $this->providers[$engineIdentifier]->getSchema();
        }

        return $this->schemas[$engineIdentifier];
    }
}
