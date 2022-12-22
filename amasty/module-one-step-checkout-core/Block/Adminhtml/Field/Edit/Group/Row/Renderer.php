<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package One Step Checkout Core for Magento 2
*/

namespace Amasty\CheckoutCore\Block\Adminhtml\Field\Edit\Group\Row;

use Amasty\CheckoutCore\Block\Adminhtml\Renderer\Template;

class Renderer extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_CheckoutCore::widget/form/renderer/row.phtml';

    /**
     * @param int $attributeId
     *
     * @return string
     */
    public function getOrderAttrUrl($attributeId)
    {
        return parent::getUrl('amorderattr/attribute/edit', ['attribute_id' => $attributeId]);
    }

    /**
     * @param int $attributeId
     *
     * @return string
     */
    public function getCustomerAttrUrl($attributeId)
    {
        return parent::getUrl('amcustomerattr/attribute/edit', ['attribute_id' => $attributeId]);
    }
}
