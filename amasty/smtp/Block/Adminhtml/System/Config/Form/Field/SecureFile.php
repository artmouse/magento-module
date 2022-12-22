<?php

declare(strict_types=1);

namespace Amasty\Smtp\Block\Adminhtml\System\Config\Form\Field;

class SecureFile extends \Magento\Config\Block\System\Config\Form\Field\File
{
    protected function _getDeleteCheckbox()
    {
        if ((string)$this->getValue()) {
            return '<br>' . __('Credentials was uploaded.');
        }

        return '';
    }
}
