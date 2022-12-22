<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Resolver;

use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Model\SocialDataList;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\View\Asset\Repository;

class GetSettings implements ResolverInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SocialDataList
     */
    private $socialDataList;

    /**
     * @var Repository
     */
    private $assetRepository;

    public function __construct(
        ConfigProvider $configProvider,
        Repository $assetRepository,
        SocialDataList $socialDataList
    ) {
        $this->configProvider = $configProvider;
        $this->socialDataList = $socialDataList;
        $this->assetRepository = $assetRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        return [
            'isEnabled' => $this->configProvider->isEnabled(),
            'title' => $this->configProvider->getLabel($storeId),
            'urlPrefix' => $this->configProvider->getUrlKey($storeId),
            'isShowLinkToolbar' => $this->configProvider->isAddToToolbar($storeId),
            'isShowLinkCategories' => $this->configProvider->isAddToMainMenu($storeId),
            'isShowLinkFooter' => $this->configProvider->isAddToFooter($storeId),
            'isGuestQuestionsAllowed' => $this->configProvider->isAllowUnregisteredCustomersAsk($storeId),
            'faqPageLayout' => $this->configProvider->getPageLayout($storeId),
            'isShowBreadcrumbs' => $this->configProvider->isShowBreadcrumbs($storeId),
            'isShowAskQuestionButton' => $this->configProvider->isShowAskQuestionOnAnswerPage($storeId),
            'questionsSort' => $this->configProvider->getQuestionsSort($storeId),
            'categoriesSort' => $this->configProvider->getCategoriesSort($storeId),
            'displayedAnswerLengthLimit' => $this->configProvider->getLimitShortAnswer($storeId),
            'isShowSearch' => $this->configProvider->isShowSearchBox($storeId),
            'noResultText' => $this->configProvider->getNoItemsLabel($storeId),
            'categoryPageSize' => $this->configProvider->getCategoryPageSize(),
            'searchPageSize' => $this->configProvider->getSearchPageSize(),
            'shortAnswerBehavior' => $this->configProvider->getFaqPageShortAnswerBehavior($storeId),
            'tagsLimit' => $this->configProvider->getTagMenuLimit($storeId),
            'isShowProductQuestionsTab' => $this->configProvider->isShowTab($storeId),
            'productQuestionsTabName' => $this->configProvider->getTabName($storeId),
            'productQuestionsTabPosition' => $this->configProvider->getTabPosition($storeId),
            'isShowAskQuestionButtonProduct' => $this->configProvider->isShowAskQuestionOnProductPage($storeId),
            'productPageSize' => $this->configProvider->getProductPageSize(),
            'shortAnswerBehaviorProduct' => $this->configProvider->getProductPageShortAnswerBehavior($storeId),
            'isRatingEnabled' => $this->configProvider->isRatingEnabled($storeId),
            'ratingType' => $this->configProvider->getVotingBehavior($storeId),
            'isHideZeroTotalRating' => $this->configProvider->isHideZeroRatingTotal($storeId),
            'isGuestRatingAllowed' => $this->configProvider->isGuestRatingAllowed($storeId),
            'isEnableUrlSuffix' => $this->configProvider->isAddUrlSuffix($storeId),
            'isRemoveTrailingSlash' => $this->configProvider->isRemoveTrailingSlash(),
            'urlSuffix' => $this->configProvider->getUrlSuffix($storeId),
            'isUseCanonicalUrl' => $this->configProvider->isCanonicalUrlEnabled($storeId),
            'isAddStructuredData' => $this->configProvider->isAddStructuredData($storeId),
            'isAddRichDataBreadcrumbs' => $this->configProvider->isAddRichDataBreadcrumbs($storeId),
            'isAddRichDataOrganization' => $this->configProvider->isAddRichDataOrganization($storeId),
            'richDataWebsiteUrl' => $this->configProvider->getRichDataWebsiteUrl($storeId),
            'richDataLogoUrl' => $this->configProvider->getRichDataLogoUrl($storeId),
            'richDataName' => $this->configProvider->getRichDataOrganizationName($storeId),
            'isAddRichDataContact' => $this->configProvider->isAddRichDataContact($storeId),
            'richDataContactType' => $this->configProvider->getRichDataContactType($storeId),
            'richDataTelephone' => $this->configProvider->getRichDataTelephone($storeId),
            'isEnableGdprConsent' => $this->configProvider->isGDPREnabled($storeId),
            'gdprConsentText' => $this->configProvider->getGDPRText($storeId),
            'isSocialButtonsEnabled' => $this->configProvider->isSocialButtonsEnabled(),
            'socialButtons' => $this->getSocialButtons()
        ];
    }

    private function getSocialButtons(): array
    {
        return array_map(function ($button) {
            return [
                'hrefTemplate' => $button->getHrefTemplate(),
                'imgName' => $button->getImgName(),
                'name' => $button->getName(),
                'imagePath' => $this->assetRepository->getUrl(
                    'Amasty_Faq::image/social/' . $button->getImgName()
                )
            ];
        }, $this->socialDataList->getActiveSocials());
    }
}
