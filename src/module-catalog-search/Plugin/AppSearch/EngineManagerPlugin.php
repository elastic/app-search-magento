<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Plugin\AppSearch;

use Elastic\AppSearch\Framework\AppSearch\EngineManagerInterface;
use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Elastic\AppSearch\Framework\AppSearch\EngineInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;

/**
 * Sync Magento search weight with the AppSearch engine.
 *
 * @package   Elastic\AppSearch\CatalogSearch\Plugin\AppSearch
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class EngineManagerPlugin
{
    /**
     * @var RequestGenerator
     */
    private $requestGenerator;

    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * @var string
     */
    private $engineIdentifier;

    /**
     * @var string
     */
    private $requestName;

    /**
     * Constructor.
     *
     * @param RequestGenerator        $requestGenerator
     * @param string                  $engineIdentifier
     * @param string                  $requestName
     */
    public function __construct(
        RequestGenerator $requestGenerator,
        FieldMapperResolverInterface $fieldMapperResolver,
        string $engineIdentifier = Fulltext::INDEXER_ID,
        string $requestName = 'quick_search_container'
    ) {
        $this->requestGenerator = $requestGenerator;
        $this->fieldMapper      = $fieldMapperResolver->getFieldMapper($engineIdentifier);
        $this->engineIdentifier = $engineIdentifier;
        $this->requestName      = $requestName;
    }

    /**
     * Sync the search fields after the schema is updated.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param EngineManagerInterface $engineManager
     * @param void                   $result
     * @param EngineInterface        $engine
     * @param SchemaInterface        $schema
     *
     * @return void
     */
    public function afterUpdateSchema(
        EngineManagerInterface $engineManager,
        $result,
        EngineInterface $engine,
        SchemaInterface $schema
    ) {
        $searchFields = array_intersect_key($this->getSearchFields(), $schema->getFields());
        $engineManager->updateSearchFields($engine, $searchFields);
    }

    /**
     * Extract search fields from the request generator.
     *
     * @return array
     */
    private function getSearchFields(): array
    {
        $searchFields = [];

        $query = $this->requestGenerator->generate()[$this->requestName]['queries']['search'];

        foreach ($query['match'] as $searchField) {
            $fieldName = $this->getSearchFieldName($searchField['field']);
            $fieldWeight = floatval($searchField['boost']);
            $searchFields[$fieldName] = ['weight' => $fieldWeight];
        }

        return $searchFields;
    }

    /**
     * Convert raw field name to use searchable one.
     *
     * @param string $fieldName
     *
     * @return string
     */
    private function getSearchFieldName(string $fieldName): string
    {
        return $this->fieldMapper->getFieldName($fieldName, ['type' => SchemaInterface::CONTEXT_SEARCH]);
    }
}
