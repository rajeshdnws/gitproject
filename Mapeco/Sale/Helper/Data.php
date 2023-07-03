<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mapeco\Sale\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
	protected $_registry;
 
	public function __construct(
		\Magento\Framework\Registry $registry,
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\ResourceConnection $conn,
		\Magento\Framework\App\Http\Context $httpContext
       
    ) {
    	
    	$this->_registry = $registry;
		 $this->conn = $conn;
		$this->httpContext = $httpContext;
    
        parent::__construct($context);
    }
	public  function erpCurl($url,$data)
  {
   $ip='https://188.64.74.82:8443/'.$url;
	$curl = curl_init();
	curl_setopt_array($curl, array(
	CURLOPT_URL => $ip,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_SSL_VERIFYPEER=>false,
	CURLOPT_SSL_VERIFYHOST=>false,
	CURLOPT_MAXREDIRS => 60,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,	
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS =>$data,
	CURLOPT_HTTPHEADER => array(
	'Content-Type: application/json'
	),
	));
	$response = curl_exec($curl);    
	
	  if (curl_errno($curl)) {
    return $error_msg = curl_error($curl);
}

	curl_close($curl);
	 return $response;
  }
  
  public function getCustomerIsLoggedIn()
	{
    	return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
	}
	public function getBesteladresnummer()
	{
    	return $this->httpContext->getValue('besteladresnummer');
	}
  public function getStock(){
  
            $connection = $this->conn->getConnection();
            $tableName = 'custom_price'; //gives table name with prefix
			$current_product = $this->_registry->registry('current_product');

            $sku=$current_product->getSku();
			   if($this->getCustomerIsLoggedIn()) {
           $factuuradresnummer=(int)$this->getBesteladresnummer();
           }
          if(empty($factuuradresnummer))$factuuradresnummer=99;
		   
			$sql = 'Select * FROM ' . $tableName.' Where addressid ='.$factuuradresnummer.' and sku="'.$sku.'"';
           return  $result = $connection->fetchRow($sql); // gives associated array, table fields as key in array.
  }
	
}
