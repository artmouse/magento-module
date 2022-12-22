<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Resolver\Question;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Model\Emails\NotifierProvider;
use Amasty\Faq\Model\OptionSource\Question\Status;
use Amasty\Faq\Model\QuestionFactory;
use Amasty\FaqGraphQl\Model\Formatter\CommonOutput;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PlaceQuestion implements ResolverInterface
{
    public const PRODUCT_ID_KEY = 'product_id';
    public const CATEGORY_ID_KEY = 'category_id';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var QuestionRepositoryInterface
     */
    private $repository;

    /**
     * @var QuestionFactory
     */
    private $questionFactory;

    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var CommonOutput
     */
    private $commonOutputFormatter;

    /**
     * @var NotifierProvider
     */
    private $notifierProvider;

    public function __construct(
        ConfigProvider $configProvider,
        QuestionRepositoryInterface $repository,
        QuestionFactory $questionFactory,
        GetCustomer $getCustomer,
        CommonOutput $commonOutputFormatter,
        NotifierProvider $notifierProvider
    ) {
        $this->configProvider = $configProvider;
        $this->repository = $repository;
        $this->questionFactory = $questionFactory;
        $this->getCustomer = $getCustomer;
        $this->commonOutputFormatter = $commonOutputFormatter;
        $this->notifierProvider = $notifierProvider;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $args = $args['input'];
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        try {
            $customerId = $this->getCustomer->execute($context)->getId();
        } catch (GraphQlNoSuchEntityException $e) {
            $customerId = 0;
        }
        if ($customerId === 0 && !$this->configProvider->isAllowUnregisteredCustomersAsk($storeId)) {
            return $this->commonOutputFormatter->format(__('Please log in to ask a question.'), true);
        }

        $model = $this->questionFactory->create();
        $model->setTitle($args[QuestionInterface::TITLE])
            ->setName($args[QuestionInterface::NAME] ?? '')
            ->setEmail($args[QuestionInterface::EMAIL] ?? '')
            ->setStatus(Status::STATUS_PENDING)
            ->setProductIds((string)$args[self::PRODUCT_ID_KEY] ?? null)
            ->setCategoryIds((string)$args[self::CATEGORY_ID_KEY] ?? null)
            ->setStoreIds($storeId)
            ->setAskedFromStore($storeId);

        $validate = $model->validate();
        if ($validate === true) {
            $this->repository->save($model);
            $notifier = $this->notifierProvider->get(NotifierProvider::TYPE_ADMIN);
            if ($notifier) {
                $notifier->notify($model);
            }
            $successMessage = (bool)$model->getEmail()
                ? __('The question was sent. We\'ll notify you about the answer via email.')
                : __('The question was sent.');
            return $this->commonOutputFormatter->format($successMessage);
        } else {
            return $this->commonOutputFormatter->format($validate[array_key_first($validate)], true);
        }
    }
}
