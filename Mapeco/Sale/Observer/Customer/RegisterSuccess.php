<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mapeco\Sale\Observer\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session;
use Mapeco\Sale\Helper\Data;
use Magento\Customer\Api\Data\CustomerInterface;
class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
	
	/**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
		
	protected $helperData;
	protected $customerSession;
	protected $logdata;



/**
* ...
* @param \Magento\Framework\Registry $registry,
*/
public function __construct(Data $helperData,Session $customerSession,LoggerInterface $logdata,CustomerRepositoryInterface $customerRepository) {
$this->helperData = $helperData;
$this->customerSession = $customerSession;
$this->logdata = $logdata;
$this->customerRepository = $customerRepository;

}

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
		$customer = $observer->getEvent()->getCustomer();
		$email = $customer->getEmail();
		$first_name = $customer->getFirstname();
        $last_name = $customer->getLastname();
        	$url='rest/insertCustomer';
			 $arrayParameters = [
					   "firmacreatie" => "MPT", 
					   "besteladresnummer" => 0, 
					   "adresnaam1" => "Test Infomat1", 
					   "adresnaam2" => "", 
					   "adresnaam3" => "", 
					   "adreslijn1" => "laarstraat 16", 
					   "adreslijn2" => "", 
					   "adreslijn3" => "", 
					   "postcode" => "2360", 
					   "localiteit" => "Wilrijk", 
					   "landcode" => "BE", 
					   "btw_nummer" => "", 
					   "telefoonnummer" => "0", 
					   "voornaam" =>$first_name, 
					   "achternaam" => $first_name, 
					   "email_adres" => $email, 
					   "taal" => "nl", 
					   "gsm_nummer" => "", 
					   "bezoekfrequentie" => "", 
					   "beslissingsmacht" => "", 
					   "departement" => "", 
					   "persoon_volgnummer" => 0 
					]; 
					
		 $reponse= $this->helperData->erpCurl($url,json_encode($arrayParameters));
		 	$erpShipmentArray=json_decode($reponse, true);
      				
	
        try {
          	 $customerupdate = $this->customerRepository->getById((int)$customer->getId());
            $customerupdate->setCustomAttribute('persoon_volgnummer',$erpShipmentArray['persoon_volgnummer']);
             $customerupdate->setCustomAttribute('besteladresnummer',$erpShipmentArray['adresnummer']);
			 $this->customerRepository->save($customerupdate);

        } catch (LocalizedException $exception) {
            $customerupdate = null;
            $this->logger->error($exception);
        }

		 $this->logdata->debug(json_encode($arrayParameters).'   '.$reponse);

    }
}