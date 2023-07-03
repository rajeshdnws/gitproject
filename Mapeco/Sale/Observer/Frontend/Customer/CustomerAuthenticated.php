<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mapeco\Sale\Observer\Frontend\Customer;

class CustomerAuthenticated implements \Magento\Framework\Event\ObserverInterface
{
	
protected $registry;
protected $customerSession;


/**
* ...
* @param \Magento\Framework\Registry $registry,
*/
public function __construct(\Magento\Framework\Registry $registry,\Magento\Customer\Model\Session $customerSession) {
$this->registry = $registry;
$this->customerSession = $customerSession;
}
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
		 
		// var_dump($this->customerSession->getCustomer()->getEntityId());
		 // echo '<pre>'; var_dump($this->customerSession->getCustomer()->getFactuuradresnummer());
		//   die;
		   
		   
        $this->registry->register('billingaddress', 104407);

        //Your observer code
    }
}

