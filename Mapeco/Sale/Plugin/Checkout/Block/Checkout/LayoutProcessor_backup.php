<?php
namespace Mapeco\Sale\Plugin\Checkout\Block\Checkout;

class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {     
        
	   
				
	 $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['before-form']['children']['customer_shipping_date'] = [
            'component' => 'Magento_Ui/js/form/element/date',
            'config' => [
                'customScope' => 'shippingAddress.extension_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/date',
                'options' => [],
                'id' => 'customer_shipping_date'
            ],
            'dataScope' => 'shippingAddress.customer_shipping_date',
            'label' => __('Shipping Delivery Date'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 100,
            'id' => 'customer_shipping_date'
        ];
     $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['before-form']['children']['customer_shipping_comments'] = [
            'component' => 'Magento_Ui/js/form/element/textarea',
            'config' => [
                'customScope' => 'shippingAddress.extension_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/textarea',
                'options' => [],
                'id' => 'customer_shipping_comments'
            ],
            'dataScope' => 'shippingAddress.customer_shipping_comments',
            'label' => __('Shipping Delivery Comment'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 200,
            'id' => 'customer_shipping_comments'
        ];
		$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['before-form']['children']['ophalen_winkel'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress.extension_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'ophalen-winkel',
            ],
            'dataScope' => 'before-shipping-method-form.ophalen_winkel',
            'label' => 'Ophalen in winkel',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 251,
            'id' => 'ophalen_winkel',
            'options' => [
                [
                    'value' => 'mpt',
                    'label' => 'Selecteer alstublieft ophalen-winkel',
                ],
                [
                    'value' => 'mpw',
                    'label' => 'Wommelgem',
                ],
				[
                    'value' => 'mpt',
                    'label' => 'Turnhout',
                ],
				[
                    'value' => 'mpv',
                    'label' => 'Vilvoorde',
                ], 
				[
                    'value' => 'mpk',
                    'label' => 'Kalmthout-Nieuwmoer',
                ]
            ]
        ]; 

       return $jsLayout;
    }
   
}
