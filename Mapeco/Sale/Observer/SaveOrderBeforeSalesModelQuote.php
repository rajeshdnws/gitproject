<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mapeco\Sale\Observer;
use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class SaveOrderBeforeSalesModelQuote implements ObserverInterface
{
    /**
     * @var Copy
     */
    public $objectCopyService;

    /**
     * SaveOrderBeforeSalesModelQuote constructor.
     * @param Copy $objectCopyService
     */
    public function __construct(
        Copy $objectCopyService
    ) {
        $this->objectCopyService = $objectCopyService;
    }

    /**
     * Copy data from quote to order after order placed
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
		
		  $quote = $observer->getEvent()->getQuote();
           $order = $observer->getEvent()->getOrder();
            $order->setCustomerShippingDate($quote->getCustomerShippingDate());
            $order->setCustomerShippingComments($quote->getCustomerShippingComments());
			 $order->setOphalenWinkel($quote->getOphalenWinkel());
        
        return $this;
    }
        
}