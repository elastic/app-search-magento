<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field\ValueMapper;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldValueMapperInterface;

/**
 * Convert raw value into the expected type.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\ValueMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
abstract class AbstractValueMapper implements FieldValueMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function mapValue($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $value = array_map([$this, 'coerceValue'], $value);

        return count($value) == 1 ? current($value) : $value;
    }

    /**
     * Coerce value to match the expected type.
     *
     * @param mixed $value
     *
     * @return string
     */
    abstract protected function coerceValue($value);
}
