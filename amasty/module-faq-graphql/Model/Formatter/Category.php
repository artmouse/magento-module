<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Formatter;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\Faq\Model\ImageProcessor;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;

class Category
{
    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var Question
     */
    private $questionFormatter;

    public function __construct(
        FilterProvider $filterProvider,
        QuestionRepositoryInterface $questionRepository,
        ImageProcessor $imageProcessor,
        Filesystem $filesystem,
        Question $questionFormatter
    ) {
        $this->filterProvider = $filterProvider;
        $this->questionRepository = $questionRepository;
        $this->imageProcessor = $imageProcessor;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->questionFormatter = $questionFormatter;
    }

    public function format(CategoryInterface $category)
    {
        $categoryData = $category->getData();

        $categoryData['description'] = $this->filterProvider->getPageFilter()->filter($category->getDescription());
        if ($category->getIcon() && $this->isCategoryIconExists($category->getIcon())) {
            $categoryData['icon'] = DIRECTORY_SEPARATOR . DirectoryList::MEDIA . DIRECTORY_SEPARATOR
                . $this->imageProcessor->getCategoryIconRelativePath($category->getIcon());
        }
        if ($category->getQuestions()) {
            $categoryData['questions'] = $this->prepareQuestions(explode(',', $category->getQuestions()));
        }
        $categoryData['store_ids'] = explode(',', $category->getStores());
        $categoryData['customer_groups'] = explode(',', $category->getCustomerGroups());

        return $categoryData;
    }

    private function isCategoryIconExists(string $iconName): bool
    {
        return $this->mediaDirectory->isExist(
            $this->imageProcessor->getCategoryIconRelativePath($iconName)
        );
    }

    private function prepareQuestions(array $categoryQuestions): array
    {
        return array_map(function ($questionId) {
            try {
                $question = $this->questionRepository->getById((int)$questionId);

                return $this->questionFormatter->format($question);
            } catch (NoSuchEntityException $e) {
                return [];
            }
        }, $categoryQuestions);
    }
}
