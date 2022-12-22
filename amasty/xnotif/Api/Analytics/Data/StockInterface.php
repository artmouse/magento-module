<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Out of Stock Notification for Magento 2
*/

namespace Amasty\Xnotif\Api\Analytics\Data;

interface StockInterface
{
    public const MAIN_TABLE = 'amasty_stock_analytics';

    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';
    public const SUBSCRIBED = 'subscribed';
    public const SENT = 'sent';
    public const ORDERS = 'orders';
    public const DATE = 'date';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return StockInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getSubscribed();

    /**
     * @param int $subscribed
     *
     * @return StockInterface
     */
    public function setSubscribed($subscribed);

    /**
     * @return int
     */
    public function getSent();

    /**
     * @param int $sent
     *
     * @return StockInterface
     */
    public function setSent($sent);

    /**
     * @return float
     */
    public function getOrders();

    /**
     * @param float $orders
     *
     * @return StockInterface
     */
    public function setOrders($orders);

    /**
     * @return string
     */
    public function getDate();

    /**
     * @param string $date
     *
     * @return StockInterface
     */
    public function setDate($date);
}
