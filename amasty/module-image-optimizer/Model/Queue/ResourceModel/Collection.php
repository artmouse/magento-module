<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Image Optimizer for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Queue\ResourceModel;

use Amasty\ImageOptimizer\Model\Queue\Queue;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Amasty\ImageOptimizer\Model\Queue\Queue::class,
            \Amasty\ImageOptimizer\Model\Queue\ResourceModel\Queue::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
        $this->setOrder(Queue::QUEUE_ID, self::SORT_ORDER_ASC);
    }
}
