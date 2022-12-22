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
use Amasty\Xnotif\Model\Product\StockSubscribe as SubscribeToStockNotification;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class StockSubscribe implements ResolverInterface
{
    /**
     * @var SubscribeToStockNotification
     */
    private $stockSubscribe;

    /**
     * @var GdprProcessor
     */
    private $gdprProcessor;

    public function __construct(
        SubscribeToStockNotification $stockSubscribe,
        GdprProcessor $gdprProcessor
    ) {
        $this->stockSubscribe = $stockSubscribe;
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
            $status = $this->stockSubscribe->execute($args['input']['product_id'], $email);
            $message = ResultStatus::MESSAGES[$status];
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getRawMessage(), $e->getParameters()));
        }

        return ['response_message' => __($message)];
    }
}
