<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mapeco\Sale\Plugin;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;

/**
 * Class ChangeShippingMethodPrice
 */
class ChangeShippingMethodPrice
{
	
	
	
	public function __construct(
    	\Magento\Customer\Model\Customer $customer,
		\Magento\Checkout\Model\Session $checkoutsession,
		\Psr\Log\LoggerInterface $logdata		

	) {
    	$this->customer = $customer;
		$this->logdata = $logdata;
		$this->checkoutsession = $checkoutsession;


	}
    /**
     * Set individual shipping price per product to each shipping rate
     *
     * @param AbstractCarrierInterface $subject
     * @param $result
     * @param RateRequest $request
     * @return mixed
     */
    public function afterCollectRates(
        AbstractCarrierInterface $subject,
        $result,
        RateRequest $request
    ) {
        if (!$result instanceof \Magento\Shipping\Model\Rate\Result) {
            return $result;
        }
        $shippingPrice=0;
		if($this->_checkUserPermission())
		{
		  foreach ($result->getAllRates() as $method) {
            $method->setPrice($shippingPrice);
			$method->setMethodTitle('Gratis levering');
			$method->setCarrierTitle('Gratis levering');
        }	
		        return $result;
	
			
		}
		
       if($request->getBaseSubtotalInclTax()>200){
		    foreach ($result->getAllRates() as $method) {
            $method->setPrice($shippingPrice);
			$method->setMethodTitle('Gratis levering');
			$method->setCarrierTitle('Bestelling vanaf € 200 excl BTW  = 0.00');
        }

      }

        return $result;
    }
	
    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _checkUserPermission() {
        if ($quote = $this->checkoutsession->getQuote()) {
            $customer = $quote->getCustomer();
			 $customerData =$this->customer->load($customer->getId());

			return $customerData->getFreeDelivery() ? true : false;
        }

        return false;
    }
}