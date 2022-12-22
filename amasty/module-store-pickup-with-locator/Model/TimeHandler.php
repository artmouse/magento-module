<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/

namespace Amasty\StorePickupWithLocator\Model;

use Amasty\Storelocator\Ui\DataProvider\Form\ScheduleDataProvider;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class TimeHandler for handle time
 */
class TimeHandler
{
    public const START_TIME = '00:00';
    public const END_TIME = '24:00';
    public const DURATION_IN_SEC = 30 * 60;
    public const DATE_FORMAT = 'Ymd';
    public const TIME_FORMAT = 'H:i';
    public const DATE_FORMAT_FOR_SAVE = 'd-m-Y';

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var array
     */
    private $isFirstSegmentDone = false;

    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @param array $scheduleArray
     * @return array
     */
    public function execute($scheduleArray)
    {
        $result = [];

        foreach ($scheduleArray as $day => $storeTime) {
            if ($scheduleArray[$day][$day . '_status']) {
                $from = $storeTime[ScheduleDataProvider::OPEN_TIME][ScheduleDataProvider::HOURS]
                    . ':' . $storeTime[ScheduleDataProvider::OPEN_TIME][ScheduleDataProvider::MINUTES];

                $breakFrom = $storeTime[ScheduleDataProvider::START_BREAK_TIME][ScheduleDataProvider::HOURS]
                    . ':' . $storeTime[ScheduleDataProvider::START_BREAK_TIME][ScheduleDataProvider::MINUTES];

                $breakTo = $storeTime[ScheduleDataProvider::END_BREAK_TIME][ScheduleDataProvider::HOURS]
                    . ':' . $storeTime[ScheduleDataProvider::END_BREAK_TIME][ScheduleDataProvider::MINUTES];

                $to = $storeTime[ScheduleDataProvider::CLOSE_TIME][ScheduleDataProvider::HOURS]
                    . ':' . $storeTime[ScheduleDataProvider::CLOSE_TIME][ScheduleDataProvider::MINUTES];

                $result[$day] = $this->getTimeRange($from, $breakFrom, $breakTo, $to);
            }
        }

        return $result;
    }

    /**
     * @param string $from
     * @param string $breakFrom
     * @param string $breakTo
     * @param string $to
     * @return array
     */
    private function getTimeRange($from, $breakFrom, $breakTo, $to)
    {
        $firstSegment = [];
        $secondSegment = [];

        if ($breakFrom == $breakTo && $breakFrom == self::START_TIME) {
            return $this->generate($from, $to);
        } else {
            for ($i = 0; $i < 2; $i++) {
                if (!$this->isFirstSegmentDone) {
                    $firstSegment = $this->generate($from, $breakFrom);
                    $this->isFirstSegmentDone = true;
                } else {
                    $secondSegment = $this->generate($breakTo, $to);
                    $this->isFirstSegmentDone = false;
                }
            }
        }

        return array_merge($firstSegment, $secondSegment);
    }

    /**
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    public function generate($startTime, $endTime)
    {
        $arrayOfTimes = [];
        $step = 0;

        $startTime = strtotime($this->getDate() . ' ' . $startTime);
        $endTime = strtotime($this->getDate() . ' ' . $endTime);
        $endTime = $endTime > $startTime ? $endTime : strtotime($this->getDate() . ' ' . self::END_TIME);

        while ($startTime + self::DURATION_IN_SEC <= $endTime) {
            $arrayOfTimes[$step]['fromInUnix'] = $startTime;
            $arrayOfTimes[$step]['label'] =
                $this->convertTime($startTime) . ' - ' . $this->convertTime($startTime + self::DURATION_IN_SEC);
            $arrayOfTimes[$step]['value'] = $startTime . '|' . ($startTime + self::DURATION_IN_SEC);
            $startTime += self::DURATION_IN_SEC;
            $arrayOfTimes[$step]['toInUnix'] = $startTime;
            $step++;
        }

        return $arrayOfTimes;
    }

    /**
     * @param string $timeStamp
     * @return string
     */
    public function convertTime($timeStamp)
    {
        return $this->timezone->formatDateTime(
            date(self::TIME_FORMAT, $timeStamp),
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::SHORT,
            null,
            'UTC'
        );
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->timezone->formatDateTime('now', \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE, 'en_US');
    }

    /**
     * @return int
     */
    public function getDateTimestamp($date = null)
    {
        return $this->timezone->date($date, null, false)->getTimestamp();
    }

    /**
     * @return string
     */
    public function getFormatDate()
    {
        return $this->timezone->getDateFormat();
    }

    public function prepareDateFormat(string $dateValue): string
    {
        $dateTimestamp = $this->getDateTimestamp($dateValue);

        return date(self::DATE_FORMAT_FOR_SAVE, $dateTimestamp);
    }
}
