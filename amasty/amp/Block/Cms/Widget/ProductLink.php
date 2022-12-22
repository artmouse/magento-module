<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Block\Cms\Widget;

use Amasty\Amp\Model\UrlConfigProvider;

class ProductLink extends \Magento\Catalog\Block\Widget\Link
{
    /**
     * @var UrlConfigProvider
     */
    private $urlConfigProvider;

    /**
     * @return false|string
     */
    public function getHref()
    {
        return $this->getUrlConfigProvider()->modifyProductPageUrl(parent::getHref());
    }

    /**
     * @return UrlConfigProvider
     */
    public function getUrlConfigProvider(): UrlConfigProvider
    {
        if (!$this->urlConfigProvider) {
            $this->urlConfigProvider = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(UrlConfigProvider::class);
        }

        return $this->urlConfigProvider;
    }
}
