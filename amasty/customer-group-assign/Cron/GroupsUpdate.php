<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/
declare(strict_types=1);

namespace Amasty\GroupAssign\Cron;

use Amasty\GroupAssign\Api\RuleRepositoryInterface;
use Amasty\GroupAssign\Model\BatchLoader;
use Amasty\GroupAssign\Model\Rule;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class GroupsUpdate
{
    public const PATH_TO_MODULE_ENABLED = 'amasty_groupassign/general/enabled';

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var CollectionFactory
     */
    private $customerCollection;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var array
     */
    private $enablingConfigByStore;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var BatchLoader
     */
    private $batchLoader;

    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        CollectionFactory $customerCollection,
        ScopeConfigInterface $scopeConfig,
        StoreRepositoryInterface $storeRepository,
        LoggerInterface $logger,
        BatchLoader $batchLoader
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->customerCollection = $customerCollection;
        $this->scopeConfig = $scopeConfig;
        $this->storeRepository = $storeRepository;
        $this->logger = $logger;
        $this->batchLoader = $batchLoader;
    }

    public function execute()
    {
        if (!$this->isModuleEnabledForAnyStore()) {
            return;
        }

        $allCustomers = $this->customerCollection->create();
        $activeRules = $this->ruleRepository->getActiveRules();

        /** @var Customer $customer */
        foreach ($this->batchLoader->load($allCustomers) as $customer) {
            $rulesToApply = [];

            /** @var Rule $rule */
            foreach ($activeRules as $rule) {
                if ($rule->getConditions()->validate($customer)) {
                    $rulesToApply[] = ['priority' => $rule->getPriority(), 'rule' => $rule];
                }
            }

            if (count($rulesToApply)
                && ($ruleToApply = $this->getRuleWithMaxPriority($rulesToApply))
                && $this->isModuleEnabledForStore((int)$customer->getData('store_id'))
            ) {
                try {
                    $allCustomers->getConnection()->update(
                        $allCustomers->getMainTable(),
                        [CustomerInterface::GROUP_ID => (int)$ruleToApply['rule']->getMoveToGroup()],
                        [$allCustomers->getIdFieldName() . ' = ?' => $customer->getId()]
                    );

                    $customer->reindex();
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }
    }

    /**
     * Rules apply regarding the lowest priority.
     * I.e. rule with priority 1 will be applied instead of rule with priority 10.
     *
     * @param array $rules
     * @return array
     */
    private function getRuleWithMaxPriority(array $rules): array
    {
        usort($rules, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $rules[0];
    }

    private function isModuleEnabledForAnyStore(): bool
    {
        return in_array(true, $this->getEnablingConfigForAllStores());
    }

    private function isModuleEnabledForStore(int $storeId): bool
    {
        $configByStore = $this->getEnablingConfigForAllStores();

        return $configByStore[$storeId] ?? false;
    }

    private function getEnablingConfigForAllStores(): array
    {
        if ($this->enablingConfigByStore === null) {
            foreach ($this->storeRepository->getList() as $store) {
                $this->enablingConfigByStore[$store->getId()] = (bool)$this->scopeConfig->getValue(
                    self::PATH_TO_MODULE_ENABLED,
                    ScopeInterface::SCOPE_STORE,
                    $store->getId()
                );
            }
        }

        return $this->enablingConfigByStore;
    }
}
