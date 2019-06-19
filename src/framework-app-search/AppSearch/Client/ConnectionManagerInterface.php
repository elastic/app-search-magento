<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Client;

use Elastic\AppSearch\Client\Client;

/**
 * Retrieve a configured and ready to go App Search client.
 *
 * @api
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface ConnectionManagerInterface
{
    /**
     * Retrieve the configured App Search client.
     *
     * @param array $options
     *
     * @return Client
     */
    public function getClient(array $options = []): Client;
}
