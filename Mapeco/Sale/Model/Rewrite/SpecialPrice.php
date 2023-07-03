<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mapeco\Sale\Model\Rewrite;


/**
 * Special price model
 */
 
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
class SpecialPrice extends \Magento\Catalog\Pricing\Price\SpecialPrice
{
    

 protected $httpContext;
 protected $conn;

   
    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $localeDate,
		\Magento\Framework\App\Http\Context $httpContext,
		\Magento\Framework\App\ResourceConnection $conn

		

    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency,$localeDate);
		$this->httpContext = $httpContext;
	     $this->conn = $conn;


    }
	public function getCustomerIsLoggedIn()
	{
    	return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
	}
	public function getBesteladresnummer()
	{
    	return $this->httpContext->getValue('besteladresnummer');
	}
   
    public function customprice($sku)
     {
             $connection = $this->conn->getConnection();
            $tableName = $connection->getTableName('custom_price'); //gives table name with prefix
        if($this->getCustomerIsLoggedIn()) {

           $factuuradresnummer=(int)$this->getBesteladresnummer();
        }
         if(empty($factuuradresnummer))$factuuradresnummer=999;
		 
		 \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug($this->product->getSku().'=='.$factuuradresnummer);

            //Select Data from table
			$sql = 'Select * FROM ' . $tableName.' Where addressid ='.$factuuradresnummer.' and sku="'.$sku.'"';
            $result = $connection->fetchRow($sql); // gives associated array, table fields as key in array.

            return $result;
            }
    /**
     * Returns special price
     *
     * @return float
     */
    public function getSpecialPrice()
    {
        $specialPrice =$this->product->getSpecialPrice();
         $price=$this->customprice($this->product->getSku());

		 if(!empty($price)){
			$specialPr=$price['promoprice'];
			
		if($specialPr>0)$specialPrice=$specialPr;
		
		 }
		 
		/// if($this->product->getSku()=='SOLIDL12')
			
        if ($specialPrice !== null && $specialPrice !== false && !$this->isPercentageDiscount()) {
            $specialPrice = $this->priceCurrency->convertAndRound($specialPrice);
        }
        return $specialPrice;
    }
    

    /**
     * @inheritdoc
     */
   public function isScopeDateInInterval()
    {
       
		return true;
    }

   
}
