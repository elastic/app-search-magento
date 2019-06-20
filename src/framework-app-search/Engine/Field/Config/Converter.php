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

use Magento\Framework\Config\ConverterInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;

/**
 * Convert app_search_fields.xml files into field config.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Converter implements ConverterInterface
{
    /**
     * List of fields that should be added to all schemas.
     *
     * @var array
     */
    private $defaultFields = [
        'deleted' => ['name' => 'deleted', 'type' => SchemaInterface::FIELD_TYPE_TEXT],
        'sync_id' => ['name' => 'sync_id', 'type' => SchemaInterface::FIELD_TYPE_TEXT],
    ];

    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $result = [];

        foreach ($source->documentElement->getElementsByTagName('engine') as $engine) {
            $engineIdentifier = (string) $engine->getAttribute('identifier');
            $result[$engineIdentifier] = $this->defaultFields;
            foreach ($engine->getElementsByTagName('field') as $field) {
                $fieldConfig = $this->getFieldConfig($field);
                $fieldName   = $fieldConfig['name'];
                $result[$engineIdentifier][$fieldName] = $fieldConfig;
            }
        }

        return $result;
    }

    /**
     * Parse field config.
     *
     * @param \DOMElement $field
     *
     * @return array
     */
    private function getFieldConfig(\DOMElement $field): array
    {
        return [
            'name' => (string) $field->getAttribute('name'),
            'type' => (string) $field->getAttribute('type'),
        ];
    }
}
