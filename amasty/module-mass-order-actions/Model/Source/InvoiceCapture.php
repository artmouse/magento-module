<?php

namespace Amasty\Oaction\Model\Source;

class InvoiceCapture implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'value' => '',
                'label' => __('Do not request Capture Case')
            ],
            [
                'value' => \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE,
                'label' => __('Capture Online')
            ],
            [
                'value' => \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE,
                'label' => __('Capture Offline')
            ]
        ];

        return $options;
    }
}
