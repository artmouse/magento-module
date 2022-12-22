<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Block\Page\Html\Header;

class Currency extends \Magento\Directory\Block\Currency
{
    /**
     * @param $code
     * @return string
     */
    public function getStoreUrlAmp($code)
    {
        return $this->_urlBuilder->getUrl('directory/currency/switch', ['currency' => $code]);
    }
}
