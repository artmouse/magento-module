<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Cart\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\Cart\Model\Source\ConfirmPopup;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    public const CONFIRM_POPUP_PATH = 'dialog_popup/confirm_popup';

    public const QUOTE_URL_KEY_PATH = 'amasty_request_quote/general/url_key';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_cart/';

    public function isMiniPage(): bool
    {
        return $this->getValue(self::CONFIRM_POPUP_PATH) == ConfirmPopup::MINI_PAGE;
    }

    public function getQuoteUrlKey(int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::QUOTE_URL_KEY_PATH, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
