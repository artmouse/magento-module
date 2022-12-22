<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

namespace Amasty\Cart\Plugin\DataPost;

use Amasty\Cart\Helper\Data;

class Replacer
{
    public const DATA_POST_AJAX = 'data-post-ajax';
    public const DATA_POST = 'data-post';
    public const REPLACE_REGEX = '(<a[^>]*(%s)?[^>]*)%s([^>]*(%s)?)';
    public const HREF_ATTR = '@href="#"@';
    public const COMPARE_REGEX = '@(<a[^>]*tocompare[^>]*)data-post([^>]*)@';
    public const WISHLIST_REGEX = '@(<a[^>]*)data-post([^>]*towishlist[^>]*)@';

    /**
     * @var Data
     */
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param string $html
     * @param array $patterns
     */
    public function dataPostReplace(&$html, $patterns = ['@' . self::DATA_POST . '@'])
    {
        if ($this->helper->isActionsAjax()) {
            foreach ($patterns as $pattern) {
                $html = preg_replace(
                    $pattern,
                    '$1' . self::DATA_POST_AJAX . '$2',
                    $html
                );
            }
            $html = preg_replace(self::HREF_ATTR, '', $html);
        }
    }
}
