<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Gdpr\Ui\Component\Listing\Columns;

class CustomerLink extends AbstractLink
{
    public const URL = 'customer/index/edit';
    public const ID_FIELD_NAME = 'customer_id';
    public const ID_PARAM_NAME = 'id';

    protected function getIdFieldName(): string
    {
        return self::ID_FIELD_NAME;
    }

    protected function getIdParamName(): string
    {
        return self::ID_PARAM_NAME;
    }

    protected function getUrl(): string
    {
        return self::URL;
    }
}
