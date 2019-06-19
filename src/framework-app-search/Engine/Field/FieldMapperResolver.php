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

use Magento\Framework\Exception\LocalizedException;

/**
 * Field mapper resolver implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldMapperResolver implements FieldMapperResolverInterface
{
    /**
     * @var FieldMapperInterface[]
     */
    private $fieldMappers;

    /**
     * Cosntructor.
     *
     * @param FieldMapperInterface[] $fieldMappers
     */
    public function __construct(array $fieldMappers = [])
    {
        $this->fieldMappers = $fieldMappers;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldMapper(string $engineIdentifier): FieldMapperInterface
    {
        if (!isset($this->fieldMappers[$engineIdentifier])) {
            throw new LocalizedException(__('Unable to locate field mapper for engine %1', $engineIdentifier));
        }

        return $this->fieldMappers[$engineIdentifier];
    }
}
