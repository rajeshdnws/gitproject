<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ulmod\OrderComment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class OrderCommentConfig implements ConfigProviderInterface
{
    /**
     *  Config Paths
     */
    public const XML_PATH_GENERAL_IS_SHOW_IN_MYACCOUNT = 'ulmod_ordercomment/general/is_show_in_myaccount';
    public const XML_PATH_GENERAL_MAX_LENGTH = 'ulmod_ordercomment/general/max_length';
    public const XML_PATH_GENERAL_FIELD_STATE = 'ulmod_ordercomment/general/state';
    
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @param    ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if show order comment to customer account
     *
     * @return bool
     */
    public function isShowCommentInAccount()
    {
          return $this->scopeConfig->getValue(
              self::XML_PATH_GENERAL_IS_SHOW_IN_MYACCOUNT,
              ScopeInterface::SCOPE_STORE
          );
    }
    
    /**
     * Get order comment max length
     *
     * @return int
     */
    public function getConfig()
    {
        return [
            'max_length' => (int) $this->scopeConfig->getValue(
                self::XML_PATH_GENERAL_MAX_LENGTH,
                ScopeInterface::SCOPE_STORE
            ),
            'um_order_comment_default_state' => (int) $this->scopeConfig->getValue(
                self::XML_PATH_GENERAL_FIELD_STATE,
                ScopeInterface::SCOPE_STORE
            )
        ];
    }
}
