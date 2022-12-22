<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Resolver\Question;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Exceptions\VotingNotAllowedException;
use Amasty\Faq\Model\Frontend\Rating\VotingService;
use Amasty\Faq\Model\OptionSource\Question\Status;
use Amasty\Faq\Model\OptionSource\Question\Visibility;
use Amasty\Faq\Model\QuestionRepository;
use Amasty\Faq\Model\Frontend\Rating\VotingRequest\VotingRequestInterfaceFactory;
use Amasty\FaqGraphQl\Model\Formatter\CommonOutput;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class RateQuestion implements ResolverInterface
{
    /**
     * @var QuestionRepository
     */
    private $repository;

    /**
     * @var VotingService
     */
    private $votingService;

    /**
     * @var VotingRequestInterfaceFactory
     */
    private $votingRequestFactory;

    /**
     * @var CommonOutput
     */
    private $commonOutputFormatter;

    public function __construct(
        QuestionRepository $repository,
        VotingService $votingService,
        VotingRequestInterfaceFactory $votingRequestFactory,
        CommonOutput $commonOutputFormatter
    ) {
        $this->repository = $repository;
        $this->votingService = $votingService;
        $this->votingRequestFactory = $votingRequestFactory;
        $this->commonOutputFormatter = $commonOutputFormatter;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $args = $args['input'];
        if (!($question = $this->getQuestion((int)$args['question_id']))) {
            return $this->commonOutputFormatter->format(__('Question doesn\'t exists.'), true);
        }
        $args['vote_type'] = strtolower($args['vote_type']);

        try {
            $this->votingService->saveVotingData(
                $this->votingRequestFactory->create(['data' => $args]),
                $question,
                $args['vote_type']
            );
        } catch (VotingNotAllowedException $e) {
            return $this->commonOutputFormatter->format(__($e->getMessage()), true);
        }

        return $this->commonOutputFormatter->format(__('You successfully voted.'));
    }

    private function getQuestion(int $questionId): ?QuestionInterface
    {
        try {
            $question = $this->repository->getById($questionId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        if ($question->getStatus() == Status::STATUS_ANSWERED
            && $question->getVisibility() != Visibility::VISIBILITY_NONE
        ) {
            return $question;
        }

        return null;
    }
}
