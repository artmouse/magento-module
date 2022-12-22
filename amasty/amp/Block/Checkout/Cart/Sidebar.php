<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Amp\Block\Checkout\Cart;

class Sidebar extends \Magento\Checkout\Block\Cart\Sidebar
{
    /**
     * @throws \Magento\Framework\Exception\SessionException
     */
    public function startSession()
    {
        $this->_checkoutSession->start();
    }

    /**
     * @return Sidebar
     * @throws \Magento\Framework\Exception\SessionException
     */
    public function _beforeToHtml()
    {
        $this->startSession();
        return parent::_beforeToHtml();
    }
}
