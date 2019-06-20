<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config;

use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;

/**
 * Reader that handle app_search_fields xml files.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FilesystemReader extends Filesystem
{
    /**
     * @var string
     */
    private const CONFIG_FILE_NAME = 'app_search_fields.xml';

    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = [
        '/engines/engine'       => 'identifier',
        '/engines/engine/field' => 'name',
    ];

    /**
     * Constructor.
     *
     * @param FileResolverInterface    $fileResolver
     * @param Converter                $converter
     * @param SchemaLocator            $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param string                   $fileName
     * @param array                    $idAttributes
     * @param string                   $domDocumentClass
     * @param string                   $defaultScope
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        string $fileName = self::CONFIG_FILE_NAME,
        array $idAttributes = [],
        string $domDocumentClass = \Magento\Framework\Config\Dom::class,
        string $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
