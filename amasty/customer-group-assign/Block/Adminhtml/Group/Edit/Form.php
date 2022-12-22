<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Block\Adminhtml\Group\Edit;

use Magento\Customer\Block\Adminhtml\Group\Edit\Form as GroupForm;
use Magento\Customer\Api\Data\GroupInterface;

class Form extends GroupForm
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $form = $this->getForm();
        if ($fieldset = $form->getElement('base_fieldset')) {
            $groupId = $this->getRequest()->getParam('id');
            if ($groupId != GroupInterface::NOT_LOGGED_IN_ID || (empty($groupId) && $groupId === null)) {
                $fieldset->addField(
                    'is_visible_on_storefront',
                    'select',
                    [
                        'name' => 'is_visible_on_storefront',
                        'label' => __('Show Customer Group on Storefront'),
                        'title' => __('Show Customer Group on Storefront'),
                        'values' => [
                            '1' => __('Yes'),
                            '0' => __('No')
                        ]
                    ]
                );

                $form->addValues([
                    'is_visible_on_storefront' => $this->isVisibleOnStorefront($groupId)
                ]);
            }
        }
    }

    private function isVisibleOnStorefront(?int $groupId): bool
    {
        $isVisibleOnStorefront = false;
        if ($groupId) {
            $group = $this->_groupRepository->getById($groupId);
            $isVisibleOnStorefront = $group->getExtensionAttributes()->getIsVisibleOnStorefront() ?? false;
        }

        return $isVisibleOnStorefront;
    }
}
