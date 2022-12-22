<?php
declare(strict_types=1);

namespace Amasty\Paction\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\TierPrice;

class Content extends Group
{
    /**
     * @var string
     */
    protected $_template = 'tier_prices.phtml';

    public function getAllGroupsId(): array
    {
        return [$this->_groupManagement->getAllCustomersGroup()->getId() => __('ALL GROUPS')];
    }
}
