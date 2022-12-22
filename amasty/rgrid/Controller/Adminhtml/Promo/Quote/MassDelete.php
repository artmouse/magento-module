<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Promotions Manager for Magento 2
*/

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\SalesRule\Api\RuleRepositoryInterface;

class MassDelete extends Action
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        Action\Context $context,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);

        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var int[]|null $ids */
        $ids = $this->getRequest()->getParam('ids');
        $resultRedirect = $this->resultRedirectFactory->create();

        if (is_array($ids)) {
            try {
                foreach ($ids as $ruleId) {
                    $this->ruleRepository->deleteById($ruleId);
                }

                $this->messageManager->addSuccessMessage(__('You deleted %1 rule(s).', count($ids)));

                return $resultRedirect->setPath('sales_rule/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t delete the rule right now. Please review the log and try again.')
                );
            }

            return $resultRedirect->setPath('sales_rule/*/');
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule(s) to delete.'));

        return $resultRedirect->setPath('sales_rule/*/');
    }
}
