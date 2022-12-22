<?php

declare(strict_types=1);

namespace Amasty\MostviewedGraphQl\Model\Resolver;

use Amasty\Mostviewed\Api\Data\PackInterface;
use Amasty\MostviewedGraphQl\Model\ProductInfo;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class BundlePacks implements ResolverInterface
{
    /**
     * @var \Amasty\Mostviewed\Block\Widget\Related
     */
    private $bundlePack;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductInfo
     */
    private $productInfo;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Amasty\Mostviewed\Helper\Config
     */
    private $config;

    /**
     * @var \Amasty\Mostviewed\Block\Checkout\Cart\Messages
     */
    private $messages;

    /**
     * @var \Amasty\Mostviewed\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var Uid
     */
    private $uidEncoder;

    public function __construct(
        \Amasty\Mostviewed\Block\Product\BundlePack $bundlePack,
        \Amasty\Mostviewed\Helper\Config $config,
        \Amasty\Mostviewed\Model\ConfigProvider $configProvider,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        ProductInfo $productInfo,
        CollectionFactory $productCollectionFactory,
        \Amasty\Mostviewed\Block\Checkout\Cart\Messages $messages,
        Uid $uidEncoder
    ) {
        $this->bundlePack = $bundlePack;
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->productInfo = $productInfo;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config = $config;
        $this->messages = $messages;
        $this->configProvider = $configProvider;
        $this->uidEncoder = $uidEncoder;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            $productId = $this->uidEncoder->decode($args['uid']);
            $product = $this->productRepository->getById($productId);
            $this->registry->register('product', $product);
        } catch (\Exception $e) {
            return ['error' => 'Wrong parameters.'];
        }

        $data = $this->getData();
        $data['main_product'] = $this->productInfo->getProductInfo($product);

        return $data ?? [];
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        if ($this->bundlePack->isBundlePacksExists()) {
            $data = $this->getConfigData();
            $packs = $this->bundlePack->getBundles();
            foreach ($packs as $pack) {
                $data['items'][] = $this->getBundlePackData($pack);
            }
        }

        return $data ?? [];
    }

    /**
     * @return array
     */
    private function getConfigData(): array
    {
        return [
            'is_top_menu_enabled' => $this->config->isTopMenuEnabled(),
            'is_display_cart_message' => $this->configProvider->isMessageInCartEnabled(),
            'is_display_cart_block' => $this->config->isBlockInCartEnabled()
        ];
    }

    /**
     * @param PackInterface $pack
     * @return array
     */
    private function getBundlePackData(PackInterface $pack): array
    {
        $data = $pack->getData();
        $data['cart_message'] = $this->messages->convertMessage($this->messages->getMessage());
        $data['items'] = $this->getProductsData($pack, explode(',', $pack->getData('product_ids')));
        $data['model'] = $pack;

        return $data;
    }

    /**
     * @param PackInterface $pack
     * @param array $ids
     * @return array
     */
    private function getProductsData(PackInterface $pack, $ids = []): array
    {
        $data = [];
        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['status', 'thumbnail', 'name', 'price', 'url_key', 'tax_class_id'], 'left')
            ->addIdFilter($ids);
        foreach ($products as $key => $product) {
            $data[$key]['product'] = $this->productInfo->getProductInfo($product);
            $data[$key]['qty'] = $pack->getChildProductQty((int) $product->getData('entity_id'));
            $data[$key]['discount_amount'] = $pack->getChildProductDiscount((int) $product->getData('entity_id'));
        }

        return $data;
    }
}
