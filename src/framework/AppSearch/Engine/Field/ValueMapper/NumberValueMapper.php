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

/**
 * Ensure numerical value type.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\ValueMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class NumberValueMapper extends AbstractValueMapper
{
    /**
     * {@inheritDoc}
     */
    protected function coerceValue($value)
    {
        return is_int($value) ? intval($value) : floatval($value);
    }
}
