<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package FAQ and Product Questions for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Faq\Controller;

class RegistryRequestParamConstants
{
    public const FAQ_TAG_PARAM = 'tag';
    public const FAQ_QUERY_PARAM = 'query';
    public const FAQ_SEARCH_PARAMS = [
        self::FAQ_TAG_PARAM,
        self::FAQ_QUERY_PARAM,
    ];
}
