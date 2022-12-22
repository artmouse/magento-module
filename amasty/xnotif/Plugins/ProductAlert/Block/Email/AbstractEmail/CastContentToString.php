<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Out of Stock Notification for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Xnotif\Plugins\ProductAlert\Block\Email\AbstractEmail;

use Magento\ProductAlert\Block\Email\AbstractEmail;

/**
 * Conversion to string when null is passed, because passing null leads to fatal
 */
class CastContentToString
{
    /**
     * @see AbstractEmail::getFilteredContent()
     *
     * @param AbstractEmail $subject
     * @param string|array|null $content
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetFilteredContent(AbstractEmail $subject, $content): array
    {
        if ($content === null) {
            $content = '';
        }

        return [$content];
    }
}
