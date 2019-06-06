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

/**
 * App Search client configuration.
 *
 * @api
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
interface ClientConfigurationInterface
{
    /**
     * URL or hostname used to access the App Search service.
     *
     * @return string
     */
    public function getApiEndpoint(): ?string;

    /**
     * Private API Key used to connect to App Search.
     *
     * @return string
     */
    public function getPrivateApiKey(): ?string;

    /**
     * Search API Key used to connect to App Search.
     *
     * @return string
     */
    public function getSearchApiKey(): ?string;

    /**
     * When debug mode is enabled, all request to the App Search service are logged.
     *
     * @return bool
     */
    public function isDebug(): bool;

    /**
     * Return intgration name for the client.
     *
     * @return string
     */
    public function getIntegrationName(): string;
}
