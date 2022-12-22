<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Out of Stock Notification for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Xnotif\Plugins\ConfigurableProduct\Block\Product\View\Type;

use Amasty\Base\Model\Serializer;
use Amasty\Xnotif\Helper\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;

class Configurable
{
    public const STOCK_STATUS = 'quantity_and_stock_status';
    public const IS_IN_STOCK = 'is_in_stock';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $allProducts = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        Manager $moduleManager,
        Serializer $serializer,
        Registry $registry,
        Config $config,
        RequestInterface $request
    ) {
        $this->moduleManager = $moduleManager;
        $this->registry = $registry;
        $this->config = $config;
        $this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function beforeGetAllowProducts($subject)
    {
        if (!$subject->hasAllowProducts()) {
            $subject->setAllowProducts($this->getAllProducts($subject));
        }

        return $subject->getData('allow_products');
    }

    /**
     * @param $subject
     * @param $html
     * @return string
     */
    public function afterFetchView($subject, $html)
    {
        $configurableLayout = ['product.info.options.configurable', 'product.info.options.swatches'];
        if (in_array($subject->getNameInLayout(), $configurableLayout)
            && !$this->moduleManager->isEnabled('Amasty_Stockstatus')
            && !$this->registry->registry('amasty_xnotif_initialization')
            && !$this->request->getParam('is_amp')
        ) {
            if (!$subject->getAllowProducts()) {
                return '';
            }

            $this->registry->register('amasty_xnotif_initialization', 1);

            /* move creating code to Amasty\Xnotif\Plugins\ConfigurableProduct\Data */
            $aStockStatus = $this->registry->registry('amasty_xnotif_data');
            $aStockStatus['changeConfigurableStatus'] = true;
            $data = $this->serializer->serialize($aStockStatus);

            $html
                = '<script type="text/x-magento-init">
                    {
                        ".product-options-wrapper": {
                                    "amnotification": {
                                        "xnotif": ' . $data . '
                                    }
                         }
                    }
                   </script>' . $html;
        }

        return $html;
    }

    /**
     * @param $subject
     * @return mixed
     */
    private function getAllProducts($subject)
    {
        $mainProduct = $subject->getProduct();
        $productId = $mainProduct->getId();

        if (!isset($this->allProducts[$productId])) {
            $products = [];
            $allProducts = $mainProduct->getTypeInstance(true)
                ->getUsedProducts($mainProduct);
            if (isset($mainProduct->getData(self::STOCK_STATUS)[self::IS_IN_STOCK])) {
                $mainProductStatus = (bool) $mainProduct->getData(self::STOCK_STATUS)[self::IS_IN_STOCK];
            } else {
                $mainProductStatus = true;
            }

            foreach ($allProducts as $product) {
                if ($this->isProductAllowed($product, $mainProductStatus)) {
                    $products[] = $product;
                }
            }
            $this->allProducts[$productId] = $products;
        }

        return $this->allProducts[$productId];
    }

    private function isProductAllowed(Product $product, bool $mainProductStatus): bool
    {
        if ($product->getStatus() != Status::STATUS_ENABLED) {
            return false;
        }

        return $mainProductStatus || !$this->config->isShowOutOfStockOnly() || !$product->getIsSalable();
    }
}
