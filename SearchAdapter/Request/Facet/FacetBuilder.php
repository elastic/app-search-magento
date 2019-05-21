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
    const MAX_SIZE = 250;

    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * Constructor.
     *
     * @param FieldMapperInterface $fieldMapper
     */
    public function __construct(FieldMapperInterface $fieldMapper)
    {
        $this->fieldMapper = $fieldMapper;
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

        if ($bucket->getType() == BucketInterface::TYPE_TERM) {
            $fieldName = $this->getFieldName($bucket->getField());
            $facet[$fieldName][] = ['type' => 'value', 'size' => self::MAX_SIZE, 'name' => $bucket->getName()];
        }

        return $facet;
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
