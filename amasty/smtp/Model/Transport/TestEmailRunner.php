<?php

namespace Amasty\Smtp\Model\Transport;

use Amasty\Smtp\Model\Logger\DebugLogger;
use Amasty\Smtp\Model\Logger\MessageLogger;
use Amasty\Smtp\Model\Provider\ConnectionProviderAdapter;
use Magento\Framework\Mail\MessageInterface;
use Laminas\Http\Client\Adapter\Socket;

class TestEmailRunner extends \Amasty\Smtp\Model\Transport
{
    public function __construct( //phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
        MessageInterface $message,
        MessageInterface $mailMessage,
        MessageLogger $messageLogger,
        DebugLogger $debugLogger,
        \Amasty\Smtp\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Smtp\Model\Config $config,
        \Magento\Framework\Registry $registry,
        Socket $socket,
        ConnectionProviderAdapter $connectionProviderAdapter,
        $host = '127.0.0.1',
        array $parameters = []
    ) {
        parent::__construct(
            $message,
            $mailMessage,
            $messageLogger,
            $debugLogger,
            $helper,
            $objectManager,
            $config,
            $registry,
            $socket,
            $connectionProviderAdapter,
            $host,
            $parameters
        );
    }
}
