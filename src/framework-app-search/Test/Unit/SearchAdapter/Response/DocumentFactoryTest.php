<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\Response;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\Response\DocumentFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\Search\Document;

/**
 * Unit test for the DocumentFactory class.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Test\Unit\SearchAdapter\Response
 * @copyright 2019 Elastic
 * @license   Open Software License ('OSL') v. 3.0
 */
class DocumentFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test a valid doc is returned.
     *
     * @testWith [{"id": {"raw": 1}, "_meta": {"score": 1}}]
     */
    public function testCreateDocument($rawDocument)
    {
        $doc = $this->getDocumentFactory()->create($rawDocument);

        $this->assertInstanceOf(DocumentInterface::class, $doc);
        $this->assertEquals($rawDocument['id']['raw'], $doc->getId());

        $customAttributes = $doc->getCustomAttributes();

        $this->assertArrayHasKey('score', $customAttributes);
        $this->assertEquals('score', $customAttributes['score']->getAttributeCode());
        $this->assertEquals($rawDocument['_meta']['score'], $customAttributes['score']->getValue());
    }

    /**
     * Document factory used during tests.
     *
     * @return DocumentFactory
     */
    private function getDocumentFactory(): DocumentFactory
    {
        $objectManager = new ObjectManager($this);

        $constructorArgs = $objectManager->getConstructArguments(DocumentFactory::class);

        $constructorArgs['attributeValueFactory']->method('create')
          ->willReturnCallback($this->createAttributeValueStub($objectManager));

        $constructorArgs['documentFactory']->method('create')
          ->willReturnCallback($this->createDocumentStub($objectManager));

        return $objectManager->getObject(DocumentFactory::class, $constructorArgs);
    }

    /**
     * Attribute value create stub.
     *
     * @return callable
     */
    private function createAttributeValueStub($objectManager): callable
    {
        return function () use ($objectManager) {
            return $objectManager->getObject(AttributeValue::class);
        };
    }

    /**
     * Document create stub.
     *
     * @return callable
     */
    private function createDocumentStub($objectManager): callable
    {
        return function ($data) use ($objectManager) {
            return $objectManager->getObject(Document::class, $data);
        };
    }
}
