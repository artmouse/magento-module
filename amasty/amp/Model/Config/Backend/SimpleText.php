<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Model\Config\Backend;

class SimpleText extends \Magento\Framework\App\Config\Value
{
    /**
     * @return \Magento\Framework\App\Config\Value
     */
    public function beforeSave()
    {
        if ($this->isValueChanged() && isset($this->_data['escaper'])) {
            /** @var \Magento\Framework\Escaper $escaper */
            $escaper = $this->_data['escaper'];
            $this->setValue($escaper->escapeHtml($this->getValue()));
        }

        return parent::beforeSave();
    }
}
