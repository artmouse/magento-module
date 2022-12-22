<?php
declare(strict_types=1);

namespace Amasty\MostviewedGraphQl\Plugin\Quote\Model\Quote;

use Amasty\Mostviewed\Model\Cart\AddProductsByIds;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;

/**
 * Allow to emulate Add Bundle behavior by adding custom option to request
 */
class AddBundleOption
{
    /**
     * @param Quote $subject
     * @param Product $product
     * @param null|float|DataObject $request
     * @param null|string $processMode
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAddProduct(
        Quote $subject,
        Product $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ) {
        if (\is_object($request) && \is_array($request->getData('options'))) {
            foreach ($request->getData('options') as $option) {
                if (\is_string($option) && $option === AddProductsByIds::BUNDLE_PACK_OPTION_CODE) {
                    $product->addCustomOption(AddProductsByIds::BUNDLE_PACK_OPTION_CODE, true);
                }
            }
        }

        return [$product, $request, $processMode];
    }
}
