<?php

namespace Amasty\ShippingBar\UI\OptionsProviders;

class Stores implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $store;

    public function __construct(\Magento\Store\Model\System\Store $store)
    {
        $this->store = $store;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->store->getStoreValuesForForm();
    }
}
