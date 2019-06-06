<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Resolve AppSearch language code from store config.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class LanguageResolver
{
    /**
     * @var NULL
     */
    private const UNIVERSAL_LANGUAGE_CODE = null;

    /**
     * @var string[]
     */
    private const SUPPORTED_LANGUAGES = [
        "da", "de", "en", "es", "fr", "it", "ja",
        "ko", "nl", "pt", "pt-br", "ru", "th", "zh",
    ];

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $scopeType;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig Scope configuration.
     * @param string               $scopeType   Lookup scope type.
     */
    public function __construct(
      ScopeConfigInterface $scopeConfig,
      string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->scopeType   = $scopeType;
    }

    /**
     * Return the App Search language code infered from the store locale config.
     *
     * @param int $storeId Store id.
     *
     * @return string
     */
    public function getLanguage(int $storeId): ?string
    {
        $language = $this->getStoreLocaleCode($storeId);

        if (!$this->isSupported($language)) {
            $language = current(explode("_", $language));
        }

        return $this->isSupported($language) ? $language : self::UNIVERSAL_LANGUAGE_CODE;
    }

    /**
     * Return the current store configured locale (e.g. en_GB, fr_FR, ...).
     *
     * @param int $storeId Store id.
     *
     * @return string
     */
    private function getStoreLocaleCode(int $storeId)
    {
        return (string) $this->scopeConfig->getValue('general/locale/code', $this->scopeType, $storeId);
    }

    /**
     * Return true if the current locale match a language defined in App Search.
     *
     * @param string $localeCode Locale code (eg. en_US, en_GB, en, ...).
     *
     * @return boolean
     */
    private function isSupported(string $localeCode)
    {
        return in_array($localeCode, self::SUPPORTED_LANGUAGES);
    }
}
