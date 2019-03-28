<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client;

/**
 * Implementation of the App Search client configuration.
 *
 * @package   Elastic\AppSearch\Client
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class ClientConfiguration implements ClientConfigurationInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Encryption\EncryptorInterface   $encryptor
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiEndpoint()
    {
        return (string) $this->scopeConfig->getValue('elastic_appsearch/client/api_endpoint');
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKey()
    {
        $encryptedApiKey = (string) $this->scopeConfig->getValue('elastic_appsearch/client/api_key');

        return empty($encryptedApiKey) ? null : (string) $this->encryptor->decrypt($encryptedApiKey);
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return (bool) $this->scopeConfig->getValue('elastic_appsearch/client/is_debug');
    }
}
