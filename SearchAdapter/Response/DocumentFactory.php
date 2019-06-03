<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Response;

use Magento\Framework\Api\Search\DocumentFactory as BaseDocumentFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * App Search search adapter response document factory.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Response
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DocumentFactory
{
    /**
     * @var BaseDocumentFactory
     */
    private $documentFactory;

    /**
     * AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     *
     * @param BaseDocumentFactory   $documentFactory
     * @param AttributeValueFactory $attributeValueFactory
     */
    public function __construct(BaseDocumentFactory $documentFactory, AttributeValueFactory $attributeValueFactory)
    {
        $this->documentFactory       = $documentFactory;
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * Create a document from an App Search result.
     *
     * @param array $rawDocument
     *
     * @return DocumentInterface
     */
    public function create(array $rawDocument): DocumentInterface
    {
        $documentId    = $rawDocument['entity_id']['raw'];
        $documentScore = (float) $rawDocument['_meta']['score'];

        $attributes = [
            'score' => $this->attributeValueFactory->create()->setAttributeCode('score')->setValue($documentScore),
        ];

        $documentData = [
            DocumentInterface::ID => $documentId,
            CustomAttributesDataInterface::CUSTOM_ATTRIBUTES => $attributes,
        ];

        return $this->documentFactory->create(['data' => $documentData]);
    }
}
