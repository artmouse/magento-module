<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Model\Product\Review;

class ReviewSummary extends \Amasty\Amp\Model\Di\Wrapper
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        $name = ''
    ) {
        parent::__construct($objectManagerInterface, \Magento\Review\Model\ReviewSummary::class);
    }
}
