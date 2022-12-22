<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Gdpr\Model\Consent\ConsentStore\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ConsentStoreCollection extends AbstractCollection
{
    public function _construct()
    {
        parent::_construct();

        $this->_init(
            \Amasty\Gdpr\Model\Consent\ConsentStore\ConsentStore::class,
            ConsentStore::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
