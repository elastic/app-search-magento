<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field;

/**
 * Used to retrieve name of a field depending on the context.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldNameResolverInterface
{
    /**
     * Return field name to use depending on the context (search, filter, ...).
     *
     * @param FieldInterface $field
     * @param array          $context
     *
     * @return string
     */
    public function getFieldName(FieldInterface $field, array $context = []): string;
}
