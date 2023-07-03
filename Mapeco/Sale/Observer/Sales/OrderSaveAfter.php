<?php
namespace Mapeco\Sale\Observer\Sales;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
class OrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
{

protected $transportBuilder;
protected $inlineTranslation;
protected $customer;

public function __construct(
\Psr\Log\LoggerInterface $logger,
CustomerRepositoryInterface $customer,
\Mapeco\Sale\Helper\Data $helperData,
PaymentInterface $paymentMethod,
TransportBuilder $transportBuilder,
StateInterface $state

) {
$this->logger = $logger;
$this->customer = $customer;
$this->helperData = $helperData;
$this->paymentMethod = $paymentMethod;
$this->transportBuilder = $transportBuilder;
$this->inlineTranslation = $state;

}

/**
* Execute observer
*
* @param \Magento\Framework\Event\Observer $observer
* @return void
* @throws \Magento\Framework\Exception\NoSuchEntityException
*/
public function execute(\Magento\Framework\Event\Observer $observer) {
$order= $observer->getData('order');
$orderId = $order->getIncrementId();

$id=$order->getUmOrderComment().'|'.$orderId;

//$this->logger->debug($id.' rajesh'.$orderId);

  $customer = $this->customer->getById($order->getCustomerId());
  if(!empty($customer->getCustomAttribute('besteladresnummer'))){
  $orderFirma=strtoupper($order->getOphalenWinkel()); 
  
  
if(empty($orderFirma)){
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
}
  
  
  $grandTotal = $order->getGrandTotal();
  $subTotal = $order->getSubtotal();
 
  // fetch specific payment information
 
  $amount = $order->getPayment()->getAmountPaid();
  $paymentMethod = $order->getPayment()->getMethod();
  $info = 'cash pay';//$order->getPayment()->getAdditionalInformation('method_title');
  $method='';
   if($order->getShippingMethod()=='tablerate_bestway')
 {
	 $method='LEV';
	 
 }else
 {
	 $method='AFH';
	 
 }
 
  $shippingaddress = $order->getShippingAddress();        
   $billingaddress = $order->getBillingAddress();

   $custLastName = $order->getCustomerLastname();
  $custFirsrName = $order->getCustomerFirstname();
 $jayParsedAry = [
   "order_no" => (string)$id, 
   "customer" => [
         "customer_no" =>(int)$order->getCustomerId(), 
         "besteladresnummer" =>(int)$customer->getCustomAttribute('besteladresnummer')->getValue(),
      ], 
   "email" => $order->getCustomerEmail(), 
   "firma" =>$orderFirma,
   "created_dt" =>$order->getCreatedAt(),
   "billingAddress" => [
            "company" => $billingaddress->getCompany(), 
            "firstname" => $custFirsrName, 
            "initials" => "", 
            "prefix" => "", 
            "lastname" => $custLastName, 
            "addressline1" => $billingaddress->getStreet()[0], 
            "addressline2" =>"", 
            "house_number" => "", 
            "house_number_addition" => "", 
            "zipcode" => $billingaddress->getPostcode(), 
            "city" => $billingaddress->getCity(), 
            "state" => $billingaddress->getRegionCode(), 
            "country" => $billingaddress->getCountryId(), 
            "addressnumber" => (int)$customer->getCustomAttribute('besteladresnummer')->getValue() 
         ], 
   "shippingAddress" => [
             "company" => $shippingaddress->getCompany(), 
            "firstname" => $shippingaddress->getFirstName(), 
            "initials" => "", 
            "prefix" => "", 
            "lastname" => $shippingaddress->getLastName(), 
            "addressline1" => $shippingaddress->getStreet()[0], 
            "addressline2" =>"", 
            "house_number" => "", 
            "house_number_addition" => "", 
            "zipcode" => $shippingaddress->getPostcode(), 
            "city" => $shippingaddress->getCity(), 
            "state" => $shippingaddress->getRegionCode(), 
            "country" => $shippingaddress->getCountryId(),
               "addressnumber" => (int)$customer->getCustomAttribute('besteladresnummer')->getValue()
            ],   
   "shippingCosts" =>$order->getShippingAmount(), 
   "subTotal" => "0", 
   "shippingMethod" =>$method, 
   "paymentMethod" =>"", 
   "externe_referentie"=>$order->getCustomerShippingComments(),
   "paymentReference" => $info
]; 
 
 
 	  	$url='rest/insertOrder';

  // Get Order Items
 
  $orderItems = $order->getAllItems();
	$discount = 0;
	$lineItem = 1;
  foreach ($orderItems as $item) {
	  
	  //Repeat Below code
	  $jayParsedAry['orderlines'][]=array("line_id"=>$lineItem,"sku"=>$item->getSku(),"qty_ordered"=>(int)$item->getQtyOrdered());
	  $lineItem++;
  
 }	 $reponse=$this->helperData->erpCurl($url,json_encode($jayParsedAry));
$this->logger->debug($reponse.' ====='.json_encode($jayParsedAry));
 $return=json_decode($reponse,true);
 if(!empty(($return['ordernummer']))){
$order->setErpOrderId($return['ordernummer']);
$order->save();
 }
 else{
	 $this->sendEmailToErp($order);
 }
  }
}

public function sendEmailToErp($order)
    {
        // this is an example and you can change template id,fromEmail,toEmail,etc as per your need.
        $templateId = '6'; // template id
        $fromEmail = 'webshop@mapeco.be';  // sender Email id
        $fromName = 'Admin';             // sender Name
        $toEmail = 'decootheo@gmail.com'; // receiver email id
 
        try {
               $templateVars = [
                'email' => $order->getCustomerEmail(),
                'orderId'=> $order->getIncrementId(),
                'Customername'=> $order->getCustomerFirstname(),
                'message'=> 'No reply received within timeout'
                
            ];
 
            $storeId = 1; 
            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();
 
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
                $cc= 'webshop@mapeco.be';
                $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->addCc($cc)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}