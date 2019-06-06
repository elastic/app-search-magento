<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine;

/**
 * AppSearch Engine Schema interface.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface SchemaInterface
{
    /**
     * App Search field types definition.
     */
    public const FIELD_TYPE_TEXT        = 'text';
    public const FIELD_TYPE_NUMBER      = 'number';
    public const FIELD_TYPE_DATE        = 'date';
    public const FIELD_TYPE_GEOLOCATION = 'geolocation';

    /**
     * App Search context types definition.
     */
    public const CONTEXT_SEARCH = 'search';
    public const CONTEXT_FILTER = 'filter';
    public const CONTEXT_SORT   = 'sort';

    /**
     * List of fields of the schema (field name as key, type as value).
     *
     * @return array
     */
    public function getFields(): array;
}
