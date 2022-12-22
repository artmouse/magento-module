<?php

namespace Amasty\Smtp\Model\Log;

use Amasty\Smtp\Model\Log;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => Log::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Log::STATUS_SENT, 'label' => __('Successfully Sent')],
            ['value' => Log::STATUS_FAILED, 'label' => __('Failed')],
        ];
    }
}
