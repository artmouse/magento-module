<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Gdpr\Model\Consent\ConsentStore\ResourceModel;

use Amasty\Gdpr\Model\Consent\ConsentStore\ConsentStore as ConsentStoreModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ConsentStore extends AbstractDb
{
    public const TABLE_NAME = 'amasty_gdpr_consents_scope';

    protected function _construct()
    {
        $this->_init(
            self::TABLE_NAME,
            ConsentStoreModel::ID
        );
    }
}
