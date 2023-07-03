<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mapeco\Sale\Block\Customer;

class Erpinvoice extends \Magento\Framework\View\Element\Template
{

  

     protected $customerSession;
	 protected $helper;


    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Mapeco\Sale\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->customerSession = $customerSession;
		$this->helper = $helper;
    }
	
	public  function getErpInvoice()
	{
       $url='rest/invoices';			 
		$fact=(int)$this->customerSession->getCustomer()->getBesteladresnummer();
		
$creatie_firma=$this->customerSession->getCustomer()->getCreatieFirma();
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
	   $parameterArray=array("firma_code"=>$orderFirma,"besteladresnummer"=>$fact,"document_status"=>8,"muntcode"=>"EUR","page_number"=>1,"datum"=>"2023-1-1","header_detail"=>"D");
		$reponse= $this->helper->erpCurl($url,json_encode($parameterArray));
	   return $erpOrdersArray=json_decode($reponse, true);
	
		
	}

}

