<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Block\Customer;

use Magento\Customer\Model\Session;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Block\Account\SortLink;

class AccountLink extends SortLink
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        Session $session,
        GroupRepositoryInterface $groupRepository,
        array $data = []
    ) {
        $this->session = $session;
        $this->groupRepository = $groupRepository;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        $groupId = $this->session->getCustomerGroupId();
        $group = $this->groupRepository->getById($groupId);
        $extensionAttributes = $group->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getIsVisibleOnStorefront()) {
            $search = ['<li class="', '</li>'];
            $replace = [
                '<li class="amgroupassign-li-nav ',
                '<span class="amgroupassign-groupcode">' . $group->getCode() . '</span></li>'
            ];

            return str_replace(
                $search,
                $replace,
                parent::_toHtml()
            );
        }

        return parent::_toHtml();
    }
}
