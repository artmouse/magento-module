<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/

declare(strict_types=1);

namespace Amasty\StorePickupWithLocator\Api;

use Amasty\Storelocator\Model\ResourceModel\Location\Collection;

interface LocationCollectionForMapProviderInterface
{
    /**
     * @return Collection
     */
    public function getCollection(): Collection;
}
