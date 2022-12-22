<?php

namespace Amasty\ShippingBar\Model\Config\Backend;

class FlagSave extends \Magento\Framework\App\Config\Value
{
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }
}
