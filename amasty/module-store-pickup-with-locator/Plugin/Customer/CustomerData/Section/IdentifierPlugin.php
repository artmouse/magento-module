<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/

namespace Amasty\StorePickupWithLocator\Plugin\Customer\CustomerData\Section;

use Amasty\StorePickupWithLocator\Model\LocationProvider;
use Amasty\StorePickupWithLocator\Model\ConfigProvider;
use Magento\Customer\CustomerData\Section\Identifier;

/**
 * Class IdentifierPlugin for correct section time updates
 */
class IdentifierPlugin
{
    /**#@+
     * Section update delay
     */
    public const TEN_MINUTES = 600;
    /**#@-*/

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Identifier $subject
     * @param array $sectionsData
     * @param null $sectionNames
     * @param bool $forceNewTimestamp
     * @return array
     */
    public function afterMarkSections(
        Identifier $subject,
        array $sectionsData,
        $sectionNames = null,
        $forceNewTimestamp = false
    ) {
        if (isset($sectionNames[LocationProvider::SECTION_NAME])) {
            $sectionsData[LocationProvider::SECTION_NAME][Identifier::SECTION_KEY] =
                time() - ($this->configProvider->getExpirableSectionLifetime() * 60 - self::TEN_MINUTES);
        }

        return $sectionsData;
    }
}
