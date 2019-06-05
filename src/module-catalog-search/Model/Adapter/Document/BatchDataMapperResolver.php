<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Document;

use Magento\Framework\Exception\LocalizedException;

/**
 * Batch data mapper resolver implementation.
 *
 * @package   Elastic\Model\Adapter\Document
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class BatchDataMapperResolver implements BatchDataMapperResolverInterface
{
    /**
     * @var BatchDataMapperInterface[]
     */
    private $mappers;

    /**
     * Constructor.
     *
     * @param BatchDataMapperInterface[] $mappers
     */
    public function __construct(array $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * {@inheritDoc}
     */
    public function getMapper(string $engineIdentifier): BatchDataMapperInterface
    {
        if (!isset($this->mappers[$engineIdentifier])) {
            throw new LocalizedException(__('Could not localize batch data mapper for engine %1', $engineIdentifier));
        }

        return $this->mappers[$engineIdentifier];
    }
}
