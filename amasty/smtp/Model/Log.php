<?php

namespace Amasty\Smtp\Model;

use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel
{
    public const STATUS_SENT    = 0;
    public const STATUS_FAILED  = 1;
    public const STATUS_PENDING = 2;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Smtp\Model\ResourceModel\Log::class);
    }
}
