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

/**
 * Allow to retrieve the batch data mapper used for a specific engine.
 *
 * @api
 *
 * @package   Elastic\Model\Adapter\Document
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface BatchDataMapperResolverInterface
{
    /**
     * Retrieve the batch data mapper for the specified engine.
     *
     * @param string $engineIdentifier
     *
     * @return BatchDataMapperInterface
     */
    public function getMapper(string $engineIdentifier): BatchDataMapperInterface;
}
