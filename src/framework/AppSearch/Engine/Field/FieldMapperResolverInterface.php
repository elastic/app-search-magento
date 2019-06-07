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
 * Field mapper resolver interface.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field:
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface FieldMapperResolverInterface
{
    /**
     * Return field mapper to be used for an engine.
     *
     * @param string $engineIdentifier
     *
     * @return string
     */
    public function getFieldMapper(string $engineIdentifier): FieldMapperInterface;
}
