<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/
declare(strict_types=1);

namespace Amasty\StorePickupWithLocator\Model;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class PickupDate
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    public function getDateFormat(): string
    {
        return $this->timezone->getDateFormat(\IntlDateFormatter::SHORT);
    }
}
