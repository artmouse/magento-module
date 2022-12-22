<?php
declare(strict_types=1);

namespace Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Faq\Gdpr;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Api\Data\QuestionInterfaceFactory;
use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Faq\Gdpr\Category\DataProcessor;
use Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Reader;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class Question implements InstallerInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuestionInterfaceFactory
     */
    private $questionFactory;

    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

    /**
     * @var DataProcessor
     */
    private $categoryDataProcessor;

    /**
     * @var string
     */
    private $fileName = 'Amasty_GdprFaqSampleData::fixtures/faq/gdpr/question.csv';

    public function __construct(
        Reader $reader,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuestionInterfaceFactory $questionFactory,
        QuestionRepositoryInterface $questionRepository,
        DataProcessor $categoryDataProcessor
    ) {
        $this->reader = $reader;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->questionFactory = $questionFactory;
        $this->questionRepository = $questionRepository;
        $this->categoryDataProcessor = $categoryDataProcessor;
    }

    public function install(): void
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->isExists($row[QuestionInterface::URL_KEY])) {
                $this->create($row);
            }
        }
    }

    /**
     * @param string $identifier
     * @return bool
     * @throws LocalizedException
     */
    private function isExists(string $identifier): bool
    {
        $this->searchCriteriaBuilder
            ->addFilter(QuestionInterface::URL_KEY, $identifier)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $result = $this->questionRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($result) > 0;
    }

    /**
     * @param array $row
     * @throws \Exception
     * @throws LocalizedException
     */
    private function create(array $row): void
    {
        $object = $this->questionFactory->create();
        $row[QuestionInterface::STORES] = explode(',', $row[QuestionInterface::STORES]);
        $categoryUrls = explode(',', $row[QuestionInterface::CATEGORIES]);
        $row[QuestionInterface::CATEGORIES] = $this->categoryDataProcessor->getCategoryIdsByUrlKeys($categoryUrls);
        $object->addData($row);

        $this->questionRepository->save($object);
    }
}
