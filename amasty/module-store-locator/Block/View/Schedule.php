<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Locator for Magento 2
*/

namespace Amasty\Storelocator\Block\View;

use Magento\Framework\View\Element\Template;

/**
 * Class Schedule
 */
class Schedule extends Template
{
    protected $_template = 'Amasty_Storelocator::schedule.phtml';

    /**
     * Show schedule if "show_schedule" is enable
     *
     * @return string
     */
    public function toHtml()
    {
        if (!$this->getLocation()->getShowSchedule() || !$this->getLocation()->getScheduleString()) {
            return '';
        }

        return parent::toHtml();
    }
}
