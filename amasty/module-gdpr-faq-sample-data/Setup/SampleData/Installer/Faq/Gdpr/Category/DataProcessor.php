<?php
declare(strict_types=1);

namespace Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Faq\Gdpr\Category;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Api\Data\CategoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

class DataProcessor
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array $categoryUrls
     * @return array
     * @throws LocalizedException
     */
    public function getCategoryIdsByUrlKeys(array $categoryUrls): array
    {
        $categoryIds = [];
        foreach ($categoryUrls as $categoryUrl) {
            $categoryId = $this->getCategoryIdByUrlKey($categoryUrl);
            if ($categoryId) {
                $categoryIds[] = $categoryId;
            }
        }

        return $categoryIds;
    }

    /**
     * @param string $urlKey
     * @return int|null
     * @throws LocalizedException
     */
    public function getCategoryIdByUrlKey(string $urlKey): ?int
    {
        $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::URL_KEY, $urlKey)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $result = $this->categoryRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return $result ? (int)$result[0]->getCategoryId() : null;
    }
}
