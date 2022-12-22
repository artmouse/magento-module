<?php

namespace Amasty\ShippingBar\Controller\Adminhtml\Profile;

use Amasty\ShippingBar\Controller\Adminhtml\AbstractProfile;

class NewAction extends AbstractProfile
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
