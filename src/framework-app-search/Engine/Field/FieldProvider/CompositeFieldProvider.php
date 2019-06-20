<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldProvider;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldProviderInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * A field provider that aggregate the output of several other fields providers.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldProvider
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class CompositeFieldProvider implements FieldProviderInterface
{
    /**
     * @var FieldInterface[]
     */
    private $fields = null;

    /**
     * @var FieldProviderInterface[]
     */
    private $providers;

    /**
     * Constructor.
     *
     * @param FieldProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields(): array
    {
        if ($this->fields === null) {
            $this->fields = [];
            foreach ($this->providers as $provider) {
                $this->fields = array_merge($this->fields, $provider->getFields());
            }
        }

        return $this->fields;
    }

    /**
     * {@inheritDoc}
     */
    public function getField(string $name): FieldInterface
    {
        $fields = $this->getFields();

        if (!isset($fields[$name])) {
            throw new LocalizedException(__('Unable to find field %1 in config', $name));
        }

        return $fields[$name];
    }
}
