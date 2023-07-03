<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mapeco\Sale\Plugin\Frontend\Magento\Catalog\Model;

class Product
{

 protected $request;
 protected $conn;
 protected $logdata;
 protected $helperData;
 protected $httpContext;
 protected $checkoutsession;


    public function __construct(
        \Magento\Framework\App\Request\Http $request,
		\Magento\Framework\App\ResourceConnection $conn,
		\Psr\Log\LoggerInterface $logdata,
		\Mapeco\Sale\Helper\Data $helperData,
		\Magento\Framework\App\Http\Context $httpContext,
		 \Magento\Checkout\Model\Session $checkoutsession
    ){      	

        $this->request = $request;
	    $this->conn = $conn;
	   	$this->logdata = $logdata;
		$this->helperData = $helperData;
		$this->httpContext = $httpContext;
		$this->checkoutsession = $checkoutsession;


    }
	
	public function getCustomerIsLoggedIn()
	{
    	return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
	}
	public function getBesteladresnummer()
	{
    	return $this->httpContext->getValue('besteladresnummer');
	}
	public function getTaxvat()
	{
    	return $this->httpContext->getValue('taxvat');
	}
	
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject,$result ) {

        if($this->getCustomerIsLoggedIn()) {

           $factuuradresnummer=(int)$this->getBesteladresnummer();
        }
	   if(empty($this->request->getControllerName())){
	   $custd=$this->checkoutsession->getQuote()->getData();
       if(empty($factuuradresnummer))$factuuradresnummer=$custd['customer_besteladresnummer'];

		 
	   }
       if(empty($factuuradresnummer))$factuuradresnummer=999;
       $updateprice=$this->customprice($factuuradresnummer, $subject->getSku());
	  if($updateprice>0)
	  {		  
     $result=$updateprice['price'];
		  
	  }  
	  else{

    $url='rest/prices';
   $parameterArray=array("tarieflijst"=>"","factuuradresnummer"=>$factuuradresnummer,"muntcode"=>"EUR", "artikelen"=>[array("artikelcode"=>$subject->getSku(), "aantal"=>1)]);

	 $reponse=$this->helperData->erpCurl($url,json_encode($parameterArray));
     $repprice=json_decode($reponse, true);
	 $newprice=0;
	 $price=0;
	 $promoprice=0;
	/// $this->logdata->debug(json_encode($parameterArray).'    '.$reponse.'=='.$factuuradresnummer.' '.$subject->getSku()); 
	if(is_array($repprice)){
	  if(!empty($this->getTaxvat())){		
	 $price=@$repprice['prijzen']['0']['VK_netto_eenheidsprijs_excl_btw'];	
	  }else{		  
	 $price=@$repprice['prijzen']['0']['VK_totaalprijs_incl_btw'];	
 
	  }

	if(@$repprice['prijzen']['0']['PROMO_totaalprijs_incl_btw']>0)
	{	
    if($repprice['prijzen']['0']['totaalprijs_incl_btw']>$repprice['prijzen']['0']['PROMO_totaalprijs_incl_btw'])
	{
	$newprice=@$repprice['prijzen']['0']['PROMO_totaalprijs_excl_btw'];
	
		if(!empty($this->getTaxvat())){
	$newprice=@$repprice['prijzen']['0']['totaalprijs_excl_btw'];
		}else{
			
	$newprice=@$repprice['prijzen']['0']['PROMO_totaalprijs_incl_btw'];
	
		}
	
	}else
	{
		if(!empty($this->getTaxvat())){
	$newprice=@$repprice['prijzen']['0']['totaalprijs_excl_btw'];
		}else{
			
	$newprice=@$repprice['prijzen']['0']['totaalprijs_incl_btw'];
	
		}
			
	}

	}
	else{
		if(!empty($this->getTaxvat())){
	    $newprice=@$repprice['prijzen']['0']['totaalprijs_excl_btw'];
		}else{
   $newprice=@$repprice['prijzen']['0']['totaalprijs_incl_btw'];
	
		}
		
	}
	
	if($this->getCustomerIsLoggedIn()) {
	$customprice=@$repprice['prijzen']['0']['PROMO_totaalprijs_excl_btw'];	

	 }else{
		 
	$customprice=@$repprice['prijzen']['0']['PROMO_totaalprijs_incl_btw'];	

	 }
	
	$promoprice=$newprice;
	 
	
	$voorraad=@$repprice['prijzen']['0']['voorraad'];
	$this->insertcustomprice($factuuradresnummer, $subject->getSku(), $price,$promoprice,$customprice,$voorraad);
	//	$this->logdata->debug($promoprice.'=='.$price); 

	}
	  }
	  

        return $result;
    }
	
	  public function afterGetSpecialPrice(\Magento\Catalog\Model\Product $subject,$result ) {	
       
        if($this->getCustomerIsLoggedIn()) {

           $factuuradresnummer=(int)$this->getBesteladresnummer();
        }
       if(empty($this->request->getControllerName())){
	   $custd=$this->checkoutsession->getQuote()->getData();
	   if(empty($factuuradresnummer))$factuuradresnummer=@$custd['customer_besteladresnummer'];
		 
	   }
       if(empty($factuuradresnummer))$factuuradresnummer=999;
        $updateprice=$this->customprice($factuuradresnummer, $subject->getSku());
		  if(!empty($updateprice))
		  {			  
			$specialPr=$updateprice['promoprice'];
			
			  
			  
		 if($specialPr>0)$result=$specialPr;

			 
			  
		  }
        return $result; //special must be less than product price otherwise it will display the product price
    }
	
	
	
  public function customprice($addressid, $sku)
  {
             $connection = $this->conn->getConnection();
            $tableName = $connection->getTableName('custom_price'); //gives table name with prefix

            //Select Data from table
			$today = date('Y-m-d');
		    $sql = 'Select * FROM ' . $tableName.' Where addressid ='.$addressid.' and sku="'.$sku.'" and updatedate="'.$today.'"';
            $result = $connection->fetchRow($sql); // gives associated array, table fields as key in array.
        // \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug($sku.'  '.$sql);
            return $result;
            }
 public function insertcustomprice($addressid, $sku, $price,$promoprice,$customprice,$voorraad)
  {
            $connection = $this->conn->getConnection();
            $tableName = $connection->getTableName('custom_price'); //gives table name with prefix
			$mpt=0;
			$mpv=0;
			$mpm=0;
			$mpk=0;
			$mpw=0;
		//	echo "<pre>"; var_dump($voorraad);
		  if(!empty($voorraad)){
	     foreach($voorraad as $stock)
			  {
		 if($stock['firma_code']=='MPM'){
			 
		  if($stock['stock_beschikbaar']>0) $mpm=$stock['stock_beschikbaar'];
		   
			  
		  }
		  if($stock['firma_code']=='MPK'){
			 
		  if($stock['stock_beschikbaar']>0) $mpk=$stock['stock_beschikbaar'];
		   
			  
		  }	  
		  
		  if($stock['firma_code']=='MPT'){
			 
		  if($stock['stock_beschikbaar']>0)$mpt=$stock['stock_beschikbaar'];		  	  
			  
		  }	 
			          

		  if($stock['firma_code']=='MPV'){
			 
		  if($stock['stock_beschikbaar']>0) $mpv=$stock['stock_beschikbaar'];
		  
			  
		  }	   
			   

		  if($stock['firma_code']=='MPW'){
			 
		  if($stock['stock_beschikbaar']>0) $mpw=$stock['stock_beschikbaar'];
		  
			  
		  }				   
			  }
		 } 
		 
		 $sqlfetch = 'Select * FROM ' . $tableName.' Where addressid ='.$addressid.' and sku="'.$sku.'"';
          $result = $connection->fetchRow($sqlfetch);
		  if(!empty($result)>0)
		  {			  
         $sql = "Update " . $tableName . " Set price='".$price."',promoprice='".$promoprice."',customprice='".$customprice."',mpt=$mpt, mpv=$mpv, mpk=$mpk, mpm=$mpm,mpw=$mpw,updatedate=now() where id =".$result['id'];
           $connection->query($sql);  
			  
		  }
		  else
		  {

        $sqlin = "Insert Into " . $tableName . " (id, addressid, sku, price,promoprice,customprice,mpt, mpv, mpk, mpm,mpw,updatedate) Values ('','".$addressid."','".$sku."','".$price."','".$promoprice."','".$customprice."',$mpt,$mpv,$mpk,$mpm,$mpw,now())";
            $connection->query($sqlin);
			
		  }
			return ['price'=>$price,'promoprice'=>$promoprice,'customprice'=>$customprice];
            }
}

 
  