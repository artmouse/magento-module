<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package One Step Checkout Core for Magento 2
*/
declare(strict_types=1);

namespace Amasty\CheckoutCore\Api;

interface CheckoutBlocksProviderInterface
{
    /**
     * @return array
     */
    public function getDefaultBlockTitles(): array;

    /**
     * @param ?int $store
     * @return array
     */
    public function getBlocksConfig(int $store = null): array;
}
