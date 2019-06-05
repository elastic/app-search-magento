<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Document\BatchDataMapper;

use Elastic\AppSearch\CatalogSearch\Model\Adapter\Document\BatchDataMapperInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Document\DataProviderInterface;

/**
 * Product batch data mapper implementation.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\Model\Adapter\Document\BatchDataMapper
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Product implements BatchDataMapperInterface
{
    /**
     * @var BatchDataMapperInterface
     */
    private $attributeMapper;

    /**
     * @var DataProviderInterface[]
     */
    private $additionalDataProviders;

    /**
     * Constructor.
     *
     * @param BatchDataMapperInterface $attributeMapper
     * @param DataProviderInterface[]  $additionalDataProviders
     */
    public function __construct(BatchDataMapperInterface $attributeMapper, array $additionalDataProviders = [])
    {
        $this->attributeMapper         = $attributeMapper;
        $this->additionalDataProviders = $additionalDataProviders;
    }

    /**
     * {@inheritDoc}
     */
    public function map(array $documentData, int $storeId): array
    {
        $documents = $this->attributeMapper->map($documentData, $storeId);

        foreach ($this->additionalDataProviders as $dataProvider) {
            foreach ($dataProvider->getData(array_keys($documents), $storeId) as $entityId => $currentData) {
                $documents[$entityId] = array_merge($documents[$entityId], $currentData);
            }
        }

        return array_values($documents);
    }
}
