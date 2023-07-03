<?php
namespace Mapeco\Customer\Rewrite\Helper;

class Data extends \Swissup\Taxvat\Helper\Data
{
   

    /**
     * Check if both VAT field and validation are enabled
     * @return bool
     */
    public function canValidateVat()
    {
		if($this->_request->getModuleName()=='customer')
		{
         return [];
		}else{
        return self::isVatFieldEnabled() && self::isValidationEnabled();
		}
    }

   
}
