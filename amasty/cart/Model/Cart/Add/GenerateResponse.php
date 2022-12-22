<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Cart\Model\Cart\Add;

use Amasty\Cart\Helper\Data as AmCartHelper;
use Amasty\Cart\Model\ConfigProvider;
use Amasty\Cart\Model\Source\Section;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Helper\Data as HelperData;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Store\Model\StoreManagerInterface;

class GenerateResponse
{
    /**
     * @var string|null
     */
    private $type;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var AmCartHelper
     */
    private $helper;

    /**
     * @var CartHelper
     */
    private $cartHelper;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var CatalogSession
     */
    private $catalogSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductInterface|null
     */
    private $product;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        AmCartHelper $helper,
        CartHelper $cartHelper,
        UrlInterface $urlBuilder,
        Cart $cart,
        HelperData $helperData,
        CatalogSession $catalogSession,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        RequestInterface $request,
        ConfigProvider $configProvider
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->cart = $cart;
        $this->helperData = $helperData;
        $this->catalogSession = $catalogSession;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->request = $request;
        $this->configProvider = $configProvider;
    }

    public function execute(string $message, array $result = []): array
    {
        switch ($this->type) {
            case Section::QUOTE:
                $cartUrl = $this->getQuoteCartUrl();
                $buttonName = __('Quote Cart');
                break;
            case Section::CART:
            default:
                $cartUrl = $this->cartHelper->getCartUrl();
                $buttonName = __('View Cart');
        }

        $result = array_merge(
            $result,
            [
                'title' => __('Information'),
                'message' => $this->updateMessage($message),
                'b1_name' => __('Continue'),
                'b2_name' => $buttonName,
                'b2_action' => 'document.location = "' . $cartUrl . '";',
                'b1_action' => 'confirmHide();',
                'checkout' => '',
                'timer' => '',
                'align' => $this->helper->getDisplayAlign()
            ]
        );

        if ($this->helper->isDisplayGoToCheckout() && $this->isCartController()) {
            $goto = __('Go to Checkout');
            $result['checkout'] =
                '<a class="checkout"
                    title="' . $goto . '"
                    data-role="proceed-to-checkout"
                    href="' . $this->helper->getUrl('checkout') . '"
                    >
                    ' . $goto . '
                </a>';
        }

        //add timer
        $time = $this->helper->getTime();
        if (0 < $time) {
            $result['timer'] .= '<span class="timer">' . '(' . $time . ')' . '</span>';
        }

        $isProductView = $this->request->getParam('product_page');
        if ($isProductView == 'true' && $this->helper->getProductButton()) {
            $categoryId = $this->request->getParam('last_category_id')
                ?? $this->catalogSession->getLastVisitedCategoryId();

            if (!$categoryId && $this->getProduct()) {
                $productCategories = $this->getProduct()->getCategoryIds();

                if (count($productCategories) > 0) {
                    $categoryId = $productCategories[0];

                    if ($categoryId == $this->storeManager->getStore()->getRootCategoryId()) {
                        if (isset($productCategories[1])) {
                            $categoryId = $productCategories[1];
                        } else {
                            $categoryId = null;
                        }
                    }
                }
            }

            if ($categoryId) {
                $category = $this->categoryFactory->create()->load($categoryId);

                if ($category) {
                    $result['b1_action'] = 'document.location = "' . $category->getUrl() . '";';
                }
            }
        }

        return $result;
    }

    private function updateMessage(string $message): string
    {
        //display count cart item
        if ($this->helper->isDisplayCount()) {
            $summary = $this->cart->getSummaryQty();
            if ($summary == 1) {
                $partOne = __('There is');
                $partTwo = __(' item');
            } else {
                $partOne = __('There are');
                $partTwo = __(' items');
            }

            switch ($this->type) {
                case Section::QUOTE:
                    $linkTitle = __('Quote Cart');
                    $itemCountTitle =  __(' in your quote cart.');
                    $cartUrl = $this->getQuoteCartUrl();
                    break;
                case Section::CART:
                default:
                    $linkTitle = __('View Cart');
                    $itemCountTitle =  __(' in your cart.');
                    $cartUrl = $this->cartHelper->getCartUrl();
            }
            $message .=
                "<p id='amcart-count' class='text'>".
                $partOne .
                ' <a href="'. $cartUrl .'" id="am-a-count" data-amcart="amcart-count" title="' . $linkTitle . '">'.
                $summary.  $partTwo .
                '</a> '
                . $itemCountTitle
                . "</p>";
        }

        //display sum price
        if ($this->helper->isDisplaySubtotal()) {
            $message .=
                '<p class="amcart-subtotal text">' .
                __('Cart Subtotal:') .
                ' <span class="am_price" data-amcart="amcart-price">'.
                $this->getSubtotalHtml() .
                '</span></p>';
        }

        return $message;
    }

    private function getSubtotalHtml(): string
    {
        $totals = $this->cart->getQuote()->getTotals();
        $subtotal = isset($totals['subtotal']) && $totals['subtotal'] instanceof Total
            ? $totals['subtotal']->getValue()
            : 0;

        return $this->helperData->formatPrice($subtotal);
    }

    private function getQuoteCartUrl(): string
    {
        return $this->urlBuilder->getUrl($this->configProvider->getQuoteUrlKey() . '/cart');
    }

    private function isCartController(): bool
    {
        return $this->type === Section::CART;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    public function setProduct(?ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function setCartModel(Cart $cartModel): void
    {
        $this->cart = $cartModel;
    }
}
