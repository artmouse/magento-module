<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/

namespace Amasty\StorePickupWithLocator\Model\Config\Source;

use Amasty\StorePickupWithLocator\Model\TimeHandler;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CutOffTime for display pickup cut-off times
 */
class CutOffTime implements OptionSourceInterface
{
    /**
     * @var TimeHandler
     */
    private $timeHandler;

    public function __construct(TimeHandler $timeHandler)
    {
        $this->timeHandler = $timeHandler;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $arrayOfTimes = [];

        $startTime = strtotime(TimeHandler::START_TIME);
        $endTime = strtotime(TimeHandler::END_TIME);

        while ($startTime < $endTime) {
            $arrayOfTimes[date(TimeHandler::TIME_FORMAT, $startTime)] = $this->timeHandler->convertTime($startTime);
            $startTime += TimeHandler::DURATION_IN_SEC;
        }

        return $arrayOfTimes;
    }
}
