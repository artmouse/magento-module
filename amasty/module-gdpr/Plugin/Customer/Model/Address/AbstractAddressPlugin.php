<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Gdpr\Plugin\Customer\Model\Address;

use Amasty\Gdpr\Model\Anonymization\AbstractType;
use Amasty\Gdpr\Model\Config;
use Magento\Customer\Model\Address\AbstractAddress;

/**
 * Plugin for country and region anonymization
 * by default Magento doesn't allow to set random values
 * to the region and country
 */
class AbstractAddressPlugin
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

    /**
     * Ignore validation if address is being anonymized
     */
    public function beforeValidate(AbstractAddress $subject)
    {
        if ($this->config->isModuleEnabled()
            && ($this->config->isAllowed(Config::ANONYMIZE)
                || $this->config->isAllowed(Config::DELETE))
            && $subject->getRegionId() === AbstractType::ANONYMIZE_REGION_ID
            && $subject->getCountryId() === AbstractType::ANONYMIZE_COUNTRY_ID
        ) {
            $subject->setData('should_ignore_validation', true);
        }
    }
}
