<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Facet;

use Magento\Framework\Search\Request\BucketInterface;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldMapperInterface;
use Magento\Framework\Search\Request\Aggregation\Range;

/**
 * Implementation of the default facet builder.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Facet
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FacetBuilder implements FacetBuilderInterface
{
    /**
     * @var int
     */
    const FACET_MAX_SIZE = 250;

    /**
     * Param used to build the facet object.
     */
    const FACET_NAME_PARAM = 'name';
    const FACET_TYPE_PARAM = 'type';
    const FACET_SIZE_PARAM = 'size';
    const FACET_RANGES_PARAM = 'ranges';

    /**
     * Facet type constants.
     */
    const FACET_TYPE_VALUE = 'value';
    const FACET_TYPE_RANGE = 'range';

    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * @var DynamicRangeProvider
     */
    private $dynamicRangeProvider;

    /**
     * Constructor.
     *
     * @param FieldMapperInterface $fieldMapper
     * @param DynamicRangeProvider $dynamicRangeProvider
     */
    public function __construct(FieldMapperInterface $fieldMapper, DynamicRangeProvider $dynamicRangeProvider)
    {
        $this->fieldMapper          = $fieldMapper;
        $this->dynamicRangeProvider = $dynamicRangeProvider;
    }

    /**
     * Build the facet array from the bucket.
     *
     * @param BucketInterface $bucket
     *
     * @return array
     */
    public function getFacet(BucketInterface $bucket): array
    {
        $facet = [];

        $fieldName  = $this->getFieldName($bucket->getField());
        $bucketType = $bucket->getType();

        if ($bucketType == BucketInterface::TYPE_TERM) {
            $facet[$fieldName][] = $this->getValueFacet($bucket);
        } elseif (in_array($bucketType, [BucketInterface::TYPE_RANGE, BucketInterface::TYPE_DYNAMIC])) {
            $facet[$fieldName][] = $this->getRangeFacet($bucket);
        }

        return $facet;
    }

    /**
     * Build value facet for the bucket.
     *
     * @param BucketInterface $bucket
     *
     * @return array
     */
    private function getValueFacet(BucketInterface $bucket): array
    {
        return [
            self::FACET_TYPE_PARAM => self::FACET_TYPE_VALUE,
            self::FACET_SIZE_PARAM => self::FACET_MAX_SIZE,
            self::FACET_NAME_PARAM => $bucket->getName(),
        ];
    }

    /**
     * Build range facet for the bucket.
     *
     * @param BucketInterface $bucket
     *
     * @return array
     */
    private function getRangeFacet(BucketInterface $bucket): array
    {
        return [
            self::FACET_TYPE_PARAM => self::FACET_TYPE_RANGE,
            self::FACET_NAME_PARAM => $bucket->getName(),
            self::FACET_RANGES_PARAM => $this->getFacetRanges($bucket),
        ];
    }

    /**
     * Retrive ranges used for the current bucket.
     *
     * @param BucketInterface $bucket
     *
     * @return array
     */
    private function getFacetRanges(BucketInterface $bucket): array
    {
        $type = $bucket->getType();
        $ranges = $type == BucketInterface::TYPE_RANGE ? $bucket->getRanges() : $this->getDynamicFacetRanges($bucket);

        return array_values(array_filter(array_map([$this, 'prepareRange'], $ranges)));
    }

    /**
     * Convert range object into facet range.
     *
     * @param Range $range
     *
     * @return array
     */
    private function prepareRange(Range $range)
    {
        $rangeValue = [];

        if ($range->getFrom() !== null) {
            $rangeValue['from'] = floatval($range->getFrom());
        }

        if ($range->getTo() !== null) {
            $rangeValue['to'] = floatval($range->getTo());
        }

        return $rangeValue;
    }

    /**
     * Generate range for a dynamic range bucket.
     *
     * @param BucketInterface $bucket
     *
     * @return Range[]
     */
    private function getDynamicFacetRanges(BucketInterface $bucket)
    {
        return $this->dynamicRangeProvider->getRanges($bucket);
    }

    /**
     * Convert the field name to match the indexed data.
     *
     * @param string $requestFieldName
     *
     * @return string
     */
    private function getFieldName(string $requestFieldName): string
    {
        return $this->fieldMapper->getFieldName($requestFieldName, ['type' => SchemaInterface::CONTEXT_FILTER]);
    }
}
