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
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config\FieldFactory;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config\Config as FieldConfig;
use Magento\Framework\Exception\LocalizedException;

/**
 * A field provider that read fields from the config (app_search_fields.xml).
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldProvider
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ConfigFieldProvider implements FieldProviderInterface
{
    /**
     * @var FieldConfig
     */
    private $fieldConfig;

    /**
     * @var string
     */
    private $engineIdentifier;

    /**
     * @var FieldInterface
     */
    private $fields = null;

    /**
     * Constructor.
     *
     * @param FieldFactory $fieldFactory
     * @param FieldConfig  $fieldConfig
     * @param string       $engineIdentifier
     */
    public function __construct(FieldFactory $fieldFactory, FieldConfig $fieldConfig, string $engineIdentifier)
    {
        $this->fieldFactory     = $fieldFactory;
        $this->fieldConfig      = $fieldConfig;
        $this->engineIdentifier = $engineIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields(): array
    {
        if ($this->fields === null) {
            $this->fields = array_map([$this, 'createField'], $this->fieldConfig->get($this->engineIdentifier));
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
            throw new LocalizedException(
                __('Unable to find field %1 in config for engine %2.', $name, $this->engineIdentifier)
            );
        }

        return $fields[$name];
    }

    /**
     * Create a field from config.
     *
     * @param array $fieldConfig
     *
     * @return FieldInterface
     */
    private function createField(array $fieldConfig): FieldInterface
    {
        return $this->fieldFactory->create($fieldConfig);
    }
}
