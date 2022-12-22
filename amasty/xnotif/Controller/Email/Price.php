<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Out of Stock Notification for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Xnotif\Controller\Email;

use Amasty\Xnotif\Model\ConfigProvider;
use Amasty\Xnotif\Model\Customer\GroupsValidator;
use Amasty\Xnotif\Model\Messages\ResultStatus;
use Amasty\Xnotif\Model\Product\GdprProcessor;
use Amasty\Xnotif\Model\Product\PriceSubscribe;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Url\Decoder;
use Psr\Log\LoggerInterface;

class Price implements ActionInterface
{
    /**
     * @var Decoder
     */
    private $decoder;

    /**
     * @var GdprProcessor
     */
    private $gdprProcessor;

    /**
     * @var PriceSubscribe
     */
    private $priceSubscribe;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GroupsValidator
     */
    private $groupsValidator;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Decoder $decoder,
        GdprProcessor $gdprProcessor,
        PriceSubscribe $priceSubscribe,
        MessageManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RequestInterface $request,
        GroupsValidator $groupsValidator,
        ConfigProvider $configProvider,
        LoggerInterface $logger
    ) {
        $this->decoder = $decoder;
        $this->gdprProcessor = $gdprProcessor;
        $this->priceSubscribe = $priceSubscribe;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->groupsValidator = $groupsValidator;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $backUrl = $this->request->getParam(ActionInterface::PARAM_NAME_URL_ENCODED);
        $groups = $this->configProvider->getAllowedPriceCustomerGroups();

        if ($this->groupsValidator->execute($groups)) {
            $data = $this->request->getParams();
            $productId = (int)$this->request->getParam('product_id');
            $guestEmail = $this->request->getParam('guest_email_price');
            $parentId = (int)$this->request->getParam('parent_id');

            if (!$backUrl) {
                return $resultRedirect->setPath('/');
            }

            try {
                $this->gdprProcessor->validateGDRP($data);
                $status = $this->priceSubscribe->execute($productId, $guestEmail, $parentId);
                $message = ResultStatus::MESSAGES[$status];
                $this->messageManager->addSuccessMessage(__($message));
                $this->gdprProcessor->logGdpr($guestEmail);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('Something went wrong.');
                $this->logger->error($e->getMessage());
            }
        }

        return $resultRedirect->setUrl($this->decoder->decode($backUrl));
    }
}
