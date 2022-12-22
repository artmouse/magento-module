<?php
declare(strict_types=1);

namespace Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Faq\Gdpr;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Api\Data\CategoryInterfaceFactory;
use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Reader;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class Category implements InstallerInterface
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
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var string
     */
    private $fileName = 'Amasty_GdprFaqSampleData::fixtures/faq/gdpr/category.csv';

    public function __construct(
        Reader $reader,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->reader = $reader;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
    }

    public function install(): void
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->isExists($row[CategoryInterface::URL_KEY])) {
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
            ->addFilter(CategoryInterface::URL_KEY, $identifier)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $result = $this->categoryRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($result) > 0;
    }

    /**
     * @param array $row
     * @throws \Exception
     * @throws LocalizedException
     */
    private function create(array $row): void
    {
        $object = $this->categoryFactory->create();
        $row[CategoryInterface::STORES] = explode(',', $row[CategoryInterface::STORES]);
        $object->addData($row);

        $this->categoryRepository->save($object);
    }
}
