<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Model\Adapter\Document;

use Elastic\AppSearch\Model\Adapter\EngineInterface;

/**
 * Used to split one entity into several doc to be indexed.
 *
 * @deprecated
 *
 * @package   Elastic\Model\AdapterDocument
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DocumentSlicer
{
    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Apply slicing to a documents list.
     *
     * @param EngineInterface $engine
     * @param array           $documents
     *
     * @return array
     */
    public function apply(EngineInterface $engine, array $documents)
    {
        foreach ($this->getConfig($engine) as $config) {
            $documents = $this->applySlicing($documents, $config['parentField'], $config['keyField']);
        }

        return $documents;
    }

    /**
     * Apply slicing on one dimension.
     *
     * @param array $documents
     * @param string $parentField
     * @param string $keyField
     *
     * @return array
     */
    private function applySlicing(array $documents, string $parentField, string $keyField)
    {
        $slicedDocuments = [];

        foreach ($documents as $document) {
            if (isset($document[$parentField])) {
                foreach ($document[$parentField] as $currentValue) {
                    if (isset($currentValue[$keyField])) {
                        $key = $currentValue[$keyField];
                        $sliceData = ['id' => $document['id'] . '_' . $key];
                        foreach ($currentValue as $field => $fieldValue) {
                            $sliceData[$field] = $fieldValue;
                        }
                        $slicedDocument = array_merge($document, $sliceData);
                        unset($slicedDocument[$parentField]);
                        $slicedDocuments[$sliceData['id']] = $slicedDocument;
                    }
                }
            }
        }

        return $slicedDocuments;
    }

    /**
     * Retrive slicer config for the current engine.
     *
     * @param EngineInterface $engine
     *
     * @return array
     */
    private function getConfig(EngineInterface $engine)
    {
        return $this->config[$engine->getIdentifier()] ?? [];
    }
}
