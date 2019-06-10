<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Test\Unit\Model\Adapter\Engine\Schema;

use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\AttributeAdapterProvider;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Api\Data\AttributeInterface;
use Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\AttributeAdapter;

/**
 * Unit test for the AttributeAdapterProvider class.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 *
 * @package   Elastic\AppSearch\CatalogSearch\Test\Unit\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class AttributeAdapterProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     * @var string
     */
    private $validAttribute = 'foo';

    /**
     * Test getting an attribute when found in the EAV config.
     */
    public function testLoadExistingAttribute()
    {
        $attributeFactory        = $this->createFactory(AttributeInterface::class, $this->never());
        $attributeAdapterFactory = $this->createFactory(AttributeAdapter::class, $this->once());

        $provider = new AttributeAdapterProvider($attributeAdapterFactory, $attributeFactory, $this->getEavConfig());

        $attributeAdapter = $provider->getAttributeAdapter($this->validAttribute);

        $this->assertInstanceOf(AttributeAdapter::class, $attributeAdapter);

        // Call a second time to check if the caching is correct (AttributeAdapterFactory should be run only once).
        $attributeAdapter = $provider->getAttributeAdapter($this->validAttribute);
    }

    /**
     * Test getting an attribute when not found in the EAV config.
     */
    public function testLoadMissingAttribute()
    {
        $attributeFactory        = $this->createFactory(AttributeInterface::class, $this->once());
        $attributeAdapterFactory = $this->createFactory(AttributeAdapter::class, $this->once());

        $provider = new AttributeAdapterProvider($attributeAdapterFactory, $attributeFactory, $this->getEavConfig());

        $attributeAdapter = $provider->getAttributeAdapter("invalid_attribute");

        $this->assertInstanceOf(AttributeAdapter::class, $attributeAdapter);

        // Call a second time to check if the caching is correct (AttributeAdapterFactory should be run only once).
        $attributeAdapter = $provider->getAttributeAdapter("invalid_attribute");
    }

    /**
     * Create a factory for a class.
     *
     * @param string                                             $class
     * @param \PHPUnit\Framework\MockObject\Matcher\InvokedCount $matcher
     * @return mixed
     */
    private function createFactory($class, $matcher = null)
    {
        $name = $class . 'Factory';

        $factory = $this->getMockBuilder($name)->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $matcher = $matcher ? $matcher : $this->any();

        $factory->expects($matcher)->method('create')->willReturn($this->createMock($class));

        return $factory;
    }

    /**
     * Load mocked eav config.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @return EavConfig
     */
    private function getEavConfig()
    {
        $config = $this->createMock(EavConfig::class);

        $config->expects($this->once())->method('getAttribute')->will(
            $this->returnCallback(function ($entityType, $attributeCode) {
                return $attributeCode == $this->validAttribute ? $this->createMock(AttributeInterface::class) : null;
            })
        );

        return $config;
    }
}
