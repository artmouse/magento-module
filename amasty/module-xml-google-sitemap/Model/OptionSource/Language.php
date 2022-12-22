<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package XML Google® Sitemap for Magento 2
*/

declare(strict_types=1);

namespace Amasty\XmlSitemap\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Zend_Locale_Data_Translation;

class Language implements OptionSourceInterface
{
    const DEFAULT_VALUE = '1';

    public function toOptionArray(): array
    {
        $options = [
            ['value' => self::DEFAULT_VALUE, 'label' => __('From Current Store Locale')]
        ];

        foreach (Zend_Locale_Data_Translation::$languageTranslation as $language => $code) {
            $options[] = [
                'value' => $code,
                'label' => $language . ' (' . $code . ')'
            ];
        }

        return $options;
    }
}
