<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Backend\Pack\Initialization\ConditionalDiscount;

use Magento\Framework\Exception\LocalizedException;

interface ValidatorInterface
{
    /**
     * Validate all conditional discounts data.
     *
     * @param array $discountsData
     * @return void
     * @throws LocalizedException
     */
    public function validate(array $discountsData): void;
}
