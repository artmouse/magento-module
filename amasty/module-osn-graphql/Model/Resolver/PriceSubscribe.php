<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_OsnGraphql
*/


declare(strict_types=1);

namespace Amasty\OsnGraphql\Model\Resolver;

use Amasty\Xnotif\Model\Messages\ResultStatus;
use Amasty\Xnotif\Model\Product\GdprProcessor;
use Amasty\Xnotif\Model\Product\PriceSubscribe as SubscribeToPriceNotification;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PriceSubscribe implements ResolverInterface
{
    /**
     * @var SubscribeToPriceNotification
     */
    private $priceSubscribe;

    /**
     * @var GdprProcessor
     */
    private $gdprProcessor;

    public function __construct(
        SubscribeToPriceNotification $priceSubscribe,
        GdprProcessor $gdprProcessor
    ) {
        $this->priceSubscribe = $priceSubscribe;
        $this->gdprProcessor = $gdprProcessor;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        try {
            $email = $args['input']['email'] ?? null;
            $data['gdrp'] = $args['input']['gdpr_agreement'] ?? false;
            $this->gdprProcessor->validateGDRP($data);
            $status = $this->priceSubscribe->execute($args['input']['product_id'], $email);
            $message = ResultStatus::MESSAGES[$status];
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getRawMessage(), $e->getParameters()));
        }

        return ['response_message' => __($message)];
    }
}
