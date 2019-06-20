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

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Config\Dom\UrnResolver;

/**
 * App Search field config schema locator.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var string
     */
    private const XSD_FILE_PATH = 'urn:elastic:framework-app-search:etc/app_search_fields.xsd';

    /**
     * @var \Magento\Framework\Config\Dom\UrnResolver
     */
    private $urnResolver;

    /**
     * Constructor.
     *
     * @param UrnResolver $urnResolver
     */
    public function __construct(UrnResolver $urnResolver)
    {
        $this->urnResolver = $urnResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema()
    {
        return $this->urnResolver->getRealPath(self::XSD_FILE_PATH);
    }

    /**
     * {@inheritDoc}
     */
    public function getPerFileSchema()
    {
        return $this->urnResolver->getRealPath(self::XSD_FILE_PATH);
    }
}
