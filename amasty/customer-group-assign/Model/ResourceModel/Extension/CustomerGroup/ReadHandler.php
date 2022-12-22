<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/
declare(strict_types=1);

namespace Amasty\GroupAssign\Model\ResourceModel\Extension\CustomerGroup;

use Amasty\GroupAssign\Model\Extension\CustomerGroup;
use Amasty\GroupAssign\Model\Extension\CustomerGroupFactory;
use Amasty\GroupAssign\Model\ResourceModel\Extension\CustomerGroup as CustomerGroupResource;
use Magento\Customer\Api\Data\GroupExtensionInterfaceFactory;
use Magento\Customer\Api\Data\GroupInterface;

class ReadHandler
{
    /**
     * @var CustomerGroupFactory
     */
    private $customerGroupFactory;

    /**
     * @var CustomerGroupResource
     */
    private $customerGroupResource;

    /**
     * @var GroupExtensionInterfaceFactory
     */
    private $groupExtensionInterfaceFactory;

    public function __construct(
        CustomerGroupFactory $customerGroupFactory,
        CustomerGroupResource $customerGroupResource,
        GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory
    ) {
        $this->customerGroupFactory = $customerGroupFactory;
        $this->customerGroupResource = $customerGroupResource;
        $this->groupExtensionInterfaceFactory = $groupExtensionInterfaceFactory;
    }

    public function execute(GroupInterface $entity): void
    {
        $groupId = $entity->getId();
        /** @var CustomerGroup $customerGroupModel */
        $customerGroupModel = $this->customerGroupFactory->create();
        $this->customerGroupResource->load($customerGroupModel, $groupId, CustomerGroup::GROUP_ID);

        $extensionAttributes = $entity->getExtensionAttributes()
            ? $entity->getExtensionAttributes()
            : $this->groupExtensionInterfaceFactory->create();

        $extensionAttributes->setIsVisibleOnStorefront($customerGroupModel->getIsVisibleOnStorefront());

        $entity->setExtensionAttributes($extensionAttributes);
    }
}
