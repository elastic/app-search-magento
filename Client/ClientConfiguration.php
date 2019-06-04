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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Module\ModuleListInterface;

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
     * @var string
     */
    private const INTEGRATION_NAME = 'magento-module';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface   $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        ModuleListInterface $moduleList
    )  {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor   = $encryptor;
        $this->moduleList  = $moduleList;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiEndpoint(): ?string
    {
        return (string) $this->scopeConfig->getValue('elastic_appsearch/client/api_endpoint');
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivateApiKey(): ?string
    {
        return $this->getApiKey('private');
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchApiKey(): ?string
    {
        return $this->getApiKey('search');
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug(): bool
    {
        return (bool) $this->scopeConfig->getValue('elastic_appsearch/client/is_debug');
    }

    /**
     * {@inheritDoc}
     */
    public function getIntegrationName(): string
    {
        return sprintf('%s:%s', self::INTEGRATION_NAME, $this->moduleList->getOne('Elastic_AppSearch')['setup_version']);
    }

    /**
     * Read an API key from the config.
     *
     * @param string $keyType
     *
     * @return string|NULL
     */
    private function getApiKey(string $keyType): ?string
    {
        $configPath = sprintf('elastic_appsearch/client/%s_api_key', $keyType);
        $apiKey     = (string) $this->scopeConfig->getValue($configPath);

        if (empty($apiKey)) {
            $apiKey = null;
        } elseif (substr($apiKey, 0, strlen($keyType)) !== $keyType) {
            $apiKey = (string) $this->encryptor->decrypt($apiKey);
        }

        return $apiKey;
    }
}
