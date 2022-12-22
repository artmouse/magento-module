<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Plugin\Customer\Model\ResourceModel\GroupRepository;

use Amasty\GroupAssign\Model\ResourceModel\Extension\CustomerGroup\ReadHandler;
use Amasty\GroupAssign\Model\ResourceModel\Extension\CustomerGroup\SaveHandler;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Customer\Model\ResourceModel\Group as GroupResource;

class Repository
{
    /**
     * @var ReadHandler
     */
    private $readHandler;

    /**
     * @var SaveHandler
     */
    private $saveHandler;

    /**
     * @var GroupResource
     */
    private $groupResource;

    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;

    public function __construct(
        ReadHandler $readHandler,
        SaveHandler $saveHandler,
        GroupResource $groupResource,
        TransactionManagerInterface $transactionManager
    ) {
        $this->readHandler = $readHandler;
        $this->saveHandler = $saveHandler;
        $this->groupResource = $groupResource;
        $this->transactionManager = $transactionManager;
    }

    public function afterGetById(
        GroupRepositoryInterface $subject,
        GroupInterface $result
    ): GroupInterface {
        $this->readHandler->execute($result);

        return $result;
    }

    public function aroundSave(
        GroupRepositoryInterface $subject,
        \Closure $proceed,
        GroupInterface $group
    ): GroupInterface {
        try {
            $this->transactionManager->start($this->groupResource->getConnection());
            $result = $proceed($group);
            $this->saveHandler->execute($result);
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }

        return $result;
    }
}
