<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ulmod\OrderComment\Plugin\Model\Order;

use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;

class AddUmOrderComment
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @param OrderExtensionFactory $extensionFactory
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        OrderExtensionFactory $extensionFactory,
        OrderFactory $orderFactory
    ) {
        $this->orderExtensionFactory = $extensionFactory;
        $this->orderFactory = $orderFactory;
    }

    /**
     * Set "um_order_comment" to order data
     *
     * @param OrderInterface $order
     *
     * @return OrderSearchResultInterface
     */
    public function setOrderComment(OrderInterface $order)
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $umOrderComment = $order->getUmOrderComment();
			$CustomerShippingDate = $order->getCustomerShippingDate();
			$CustomerShippingDate = $order->getCustomerShippingDate();
			$OphalenWinkel = $order->getOphalenWinkel();
        } else {
            $orderModel = $this->orderFactory->create();
            $orderModel->load($order->getId());
            $umOrderComment = $orderModel->getUmOrderComment();
			$CustomerShippingDate = $orderModel->getCustomerShippingDate();
			$CustomerShippingDate = $orderModel->getCustomerShippingDate();
			$OphalenWinkel = $orderModel->getOphalenWinkel();
        }

        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtensionAttributes = $extensionAttributes ? $extensionAttributes
            : $this->orderExtensionFactory->create();
            
        $orderExtensionAttributes->setCustomerShippingDate($CustomerShippingDate);
		$orderExtensionAttributes->setCustomerShippingDate($CustomerShippingDate);
        $orderExtensionAttributes->setOphalenWinkel($OphalenWinkel);
        $orderExtensionAttributes->setUmOrderComment($umOrderComment);

        
        $order->setExtensionAttributes($orderExtensionAttributes);
    }
    
    /**
     * Add "um_order_comment" extension attribute to order data object
     *
     * To make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $orderSearchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ) {
        foreach ($orderSearchResult->getItems() as $order) {
            $this->setOrderComment($order);
        }
        return $orderSearchResult;
    }

    /**
     * Add "um_order_comment" extension attribute to order data object
     *
     * To make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     *
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $this->setOrderComment($resultOrder);
        return $resultOrder;
    }
}
