<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Gdpr\Plugin\Directory\Model\ResourceModel;

use Amasty\Gdpr\Model\Anonymization\AbstractType;
use Amasty\Gdpr\Model\Config;
use Magento\Directory\Model\ResourceModel\Country;

/**
 * Plugin to allow loading of anonymized customer address
 * because country and region code is changed
 */
class CountryPlugin
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function aroundLoadByCode(
        Country $subject,
        \Closure $proceed,
        \Magento\Directory\Model\Country $country,
        $code
    ) {
        if ($this->config->isModuleEnabled()
            && ($this->config->isAllowed(Config::ANONYMIZE)
                || $this->config->isAllowed(Config::DELETE))
            && $code === AbstractType::ANONYMIZE_COUNTRY_ID
        ) {
            $country->setName('anonymous');
            $country->setNameDefault('anonymous');

            return $country;
        }

        return $proceed($country, $code);
    }
}
