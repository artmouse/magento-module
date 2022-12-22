<?php

declare(strict_types=1);

namespace Amasty\MostviewedGraphQl\Model;

use \Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CatalogUrlRewrite\Model\Storage\DbStorage;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class ProductInfo
{
    /**
     * @var \Magento\Catalog\Block\Product\AbstractProduct
     */
    private $abstractProduct;

    /**
     * @var DbStorage
     */
    private $urlFinder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Catalog\Block\Product\AbstractProduct $abstractProduct,
        DbStorage $urlFinder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->abstractProduct = $abstractProduct;
        $this->urlFinder = $urlFinder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    public function getProductInfo(ProductInterface $product): array
    {
        $data = $product->getData();
        $data['id'] = $data['id'] ?? $data['entity_id'];
        $data['add_to_cart_url'] = $this->abstractProduct->getAddToCartUrl($product);
        $data['add_to_wishlist'] = $this->abstractProduct->getAddToWishlistParams($product);
        $data['product_url'] = $this->getProductUrl($product);
        $data['model'] = $product;

        return $data ?? [];
    }

    /**
     * @param ProductInterface $product
     * @return mixed
     */
    private function getProductUrl(ProductInterface $product)
    {
        $requestPath = $product->getRequestPath();
        if (!$requestPath) {
            $product->getProductUrl();
            $requestPath = $product->getRequestPath();
        }

        return $requestPath;
    }

    /**
     * @param $product
     * @return string
     */
    public function getReviewsSummary($product)
    {
        return $this->abstractProduct->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
    }
}
