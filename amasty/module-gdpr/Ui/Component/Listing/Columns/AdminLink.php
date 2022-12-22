<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Gdpr\Ui\Component\Listing\Columns;

class AdminLink extends AbstractLink
{
    public const URL = 'adminhtml/user/edit';
    public const ID_FIELD_NAME = 'last_edited_by';
    public const ID_PARAM_NAME = 'user_id';

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
