<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Out of Stock Notification for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Xnotif\Model\Product;

use Amasty\Xnotif\Model\Product\Subscribe\PrepareModelForSave;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ProductAlert\Model\PriceFactory;
use Magento\ProductAlert\Model\ResourceModel\Price;
use Magento\ProductAlert\Model\ResourceModel\Price\Collection;
use Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class PriceSubscribe
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PriceFactory
     */
    private $priceFactory;

    /**
     * @var CollectionFactory
     */
    private $priceCollectionFactory;

    /**
     * @var PrepareModelForSave
     */
    private $prepareModelForSave;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Price
     */
    private $priceResource;

    public function __construct(
        StoreManagerInterface $storeManager,
        PriceFactory $priceFactory,
        CollectionFactory $priceCollectionFactory,
        PrepareModelForSave $prepareModelForSave,
        ProductRepositoryInterface $productRepository,
        Price $priceResource
    ) {
        $this->storeManager = $storeManager;
        $this->priceFactory = $priceFactory;
        $this->priceCollectionFactory = $priceCollectionFactory;
        $this->prepareModelForSave = $prepareModelForSave;
        $this->productRepository = $productRepository;
        $this->priceResource = $priceResource;
    }

    /**
     * @param int $productId
     * @param string|null $guestEmail
     * @param int|null $parentId
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(int $productId, ?string $guestEmail = null, ?int $parentId = null): int
    {
        $parentId = $productId === $parentId ? null : $parentId;
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $collection = $this->getAlertCollection($websiteId, $productId);
        $product = $this->productRepository->getById($productId);
        $model = $this->priceFactory->create()->setPrice($product->getFinalPrice());
        list($model, $status) = $this->prepareModelForSave
            ->execute($productId, $model, $collection, $guestEmail, $parentId);

        if ($model) {
            $this->priceResource->save($model);
        }

        return $status;
    }

    private function getAlertCollection(int $websiteId, int $productId): Collection
    {
        return $this->priceCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('status', 0)
            ->setCustomerOrder();
    }
}
