<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Engine\Schema;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeInterfaceFactory;
use Magento\Eav\Api\Data\AttributeInterface;

/**
 * Allow to retrive attribute to be used to refer to search engine fields.
 *
 * @package   Elastic\Model\Adapter\Engine\Schema
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeAdapterProvider
{
    /**
     * @var AttributeAdapter[]
     */
    private $cachedPool = [];

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var AttributeAdapterFactory
     */
    private $attributeAdapterFactory;

    /**
     * @var AttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * @var string
     */
    private $entityTypeId;

    /**
     * Constructor.
     *
     * @param AttributeAdapterFactory   $attributeAdapterFactory
     * @param AttributeInterfaceFactory $attributeFactory
     * @param EavConfig                 $eavConfig
     * @param string $entityTypeId
     */
    public function __construct(
        AttributeAdapterFactory $attributeAdapterFactory,
        AttributeInterfaceFactory $attributeFactory,
        EavConfig $eavConfig,
        ?string $entityTypeId = ProductAttributeInterface::ENTITY_TYPE_CODE
    ) {
        $this->eavConfig               = $eavConfig;
        $this->attributeAdapterFactory = $attributeAdapterFactory;
        $this->attributeFactory        = $attributeFactory;
        $this->entityTypeId            = $entityTypeId;
    }

    /**
     * Load an search engine attribute adapter by attribute code.
     *
     * @return AttributeAdapter
     */
    public function getAttributeAdapter(string $attributeCode, array $attributeData = []): AttributeAdapter
    {
        if (!isset($this->cachedPool[$attributeCode])) {
            $attribute = $this->getAttribute($attributeCode);

            if (null == $attribute) {
                $attribute = $this->createAttribute($attributeCode, $attributeData);
            }

            $this->cachedPool[$attributeCode] = $this->attributeAdapterFactory->create(['attribute' => $attribute]);
        }

        return $this->cachedPool[$attributeCode];
    }

    /**
     * Try to load an attribute from the EAV configuration.
     *
     * @return AttributeInterface
     */
    private function getAttribute(string $attributeCode): AttributeInterface
    {
        return $this->eavConfig ? $this->eavConfig->getAttribute($this->entityTypeId, $attributeCode) : null;
    }

    /**
     * Create an empty dummy attribute (used for fields that are not present in the EAV model).
     *
     * @return AttributeInterface
     */
    private function createAttribute(string $attributeCode, array $attributeData = []): AttributeInterface
    {
        $attributeData['attribute_code'] = $attributeCode;

        return $this->attributeFactory->create(['data' => $attributeCode]);
    }
}
