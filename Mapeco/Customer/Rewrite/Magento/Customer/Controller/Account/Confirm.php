<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mapeco\Customer\Rewrite\Magento\Customer\Controller\Account;
use Magento\Framework\Controller\ResultFactory;

class Confirm extends \Magento\Customer\Controller\Account\Confirm
{
	  private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;
	
	 private function getCustomerId(): int
    {
        return (int)$this->getRequest()->getParam('id', 0);
    }
	 public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->session->isLoggedIn()) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        $customerId = $this->getCustomerId();
        $key = $this->getRequest()->getParam('key', false);
        if (empty($customerId) || empty($key)) {
            $this->messageManager->addErrorMessage(__('Bad request.'));
            $url = $this->urlModel->getUrl('*/*/index', ['_secure' => true]);
            return $resultRedirect->setUrl($this->_redirect->error($url));
        }

        try {
            // log in and send greeting email
            $customerEmail = $this->customerRepository->getById($customerId)->getEmail();
            $customer = $this->customerAccountManagement->activate($customerEmail, $key);			
            $successMessage = $this->getSuccessMessage();
            $this->session->setCustomerDataAsLoggedIn($customer);
            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }

            if ($successMessage) {
                $this->messageManager->addSuccess($successMessage);
				
		        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $addressRepository = $objectManager->get('Magento\Customer\Api\AddressRepositoryInterface');
				$billingAddressId = $customer->getDefaultBilling();
                $address = $addressRepository->getById($billingAddressId);
				
		     $address1='';
			 $address2='';
			 $n=0;
			if(!empty($address->getStreet())){
				foreach($address->getStreet() as $key=>$addr)
				{
				if($n==0){$address1=$addr;}
				else{					
				$address2=$addr;
				}
                $n++;				
				}
				
			}
			
			
			$orderFirma='';
			$creatie_firma=$customer->getCustomAttribute('creatie_firma')->getValue();
			if($creatie_firma=='9485')
			{
			$orderFirma = "MPV";  

			}
			elseif($creatie_firma=='9486')
			{
			$orderFirma = "MPT"; 

			}
			elseif($creatie_firma=='9487')
			{
			$orderFirma = "MPK"; 

			}
			elseif($creatie_firma=='9488')
			{
			$orderFirma = "MPW"; 

			}

			else{
			$orderFirma = "MPT"; 


			}
			
			$email = $customer->getEmail();
			$first_name = $customer->getFirstname();
			$last_name = $customer->getLastname();
				$url='rest/insertCustomer';
			$arrayParameters = [
						   "firmacreatie" => $orderFirma, 
						   "besteladresnummer" => 0, 
						   "adresnaam1" => $address->getCompany(),  
						   "adresnaam2" => "", 
						   "adresnaam3" => "", 
						   "adreslijn1" => $address1,  
						   "adreslijn2" => $address2, 
						   "adreslijn3" => "", 
						   "postcode" => $address->getPostcode(), 
						   "localiteit" => $address->getCity(),
						   "btw_nummer" => $address->getVatId(), 
						   "telefoonnummer" =>$address->getTelephone(), 
						   "voornaam" =>$first_name, 
						   "achternaam" => $last_name, 
						   "email_adres" => $email, 
						   "landcode" => $address->getCountryId(), 
						   "taal" => "nl",
						   "gsm_nummer" => "", 
						   "bezoekfrequentie" => "", 
						   "beslissingsmacht" => "", 
						   "departement" => "", 
						   "persoon_volgnummer" => 0 
						]; 
                        $customerRepository = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
						$helperData = $objectManager->get('Mapeco\Sale\Helper\Data');
						$logdata = $objectManager->get('Psr\Log\LoggerInterface');
					$reponse= $helperData->erpCurl($url,json_encode($arrayParameters));
					$erpShipmentArray=json_decode($reponse, true);
					$customerupdate = $customerRepository->getById($customerId);
					if(!empty($erpShipmentArray['persoon_volgnummer'])){
					$customerupdate->setCustomAttribute('persoon_volgnummer',$erpShipmentArray['persoon_volgnummer']);
					}
					if(!empty($erpShipmentArray['adresnummer'])){
					$customerupdate->setCustomAttribute('besteladresnummer',$erpShipmentArray['adresnummer']);
					}
					
					$customerRepository->save($customerupdate);
      				$logdata->debug(json_encode($arrayParameters).'   '.$reponse);
				
            }

            $resultRedirect->setUrl($this->getSuccessRedirect());
            return $resultRedirect;
        } catch (StateException $e) {
            $this->messageManager->addException($e, __('This confirmation key is invalid or has expired.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('There was an error confirming the account'));
        }

        $url = $this->urlModel->getUrl('*/*/index', ['_secure' => true]);
        return $resultRedirect->setUrl($this->_redirect->error($url));
    }
	
	/**
     * Retrieve cookie manager
     *
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

}

