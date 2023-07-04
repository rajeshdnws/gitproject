<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ulmod\OrderComment\Model\Data;

use Ulmod\OrderComment\Api\Data\OrderCommentInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class OrderComment extends AbstractSimpleObject implements OrderCommentInterface
{
    public const COMMENT_FIELD_NAME = 'um_order_comment';
	
    public const COMMENT_FIELD_SHIP = 'customer_shipping_comments';
	public const COMMENT_FIELD_DATE = 'customer_shipping_date';
	public const COMMENT_FIELD_OP = 'ophalen_winkel';
    /**
     * Get comment
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->_get(static::COMMENT_FIELD_NAME);
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        return $this->setData(static::COMMENT_FIELD_NAME, $comment);
    }
	
	/**
     * Get customer_shipping_comments
     *
     * @return string|null
     */
	 public function getCustomerShippingComments()
	 {
		 
	 return $this->_get(static::COMMENT_FIELD_SHIP);
 
		 
	 }

    /**
     * Set customer_shipping_comments
     *
     * @param string $customer_shipping_comments
     * @return null
     */
    public function setCustomerShippingComments($customer_shipping_comments)
	{
		
		 return $this->setData(static::COMMENT_FIELD_SHIP, $customer_shipping_comments);
		
	}
	 /**
     * Get customer_shipping_date
     *
     * @return string|null
     */
    public function getCustomerShippingDate()
	{
		
		
			 return $this->_get(static::COMMENT_FIELD_DATE);

		
	}

    /**
     * Set customer_shipping_date
     *
     * @param string $customer_shipping_date
     * @return null
     */
    public function setCustomerShippingDate($customer_shipping_date)
	{
		
		 return $this->setData(static::COMMENT_FIELD_DATE, $customer_shipping_date);	
		
	}
	 /**
     * Get ophalen_winkel
     *
     * @return string|null
     */
    public function getOphalenWinkel()
	{
	 return $this->_get(static::COMMENT_FIELD_OP);
	
		
	}

    /**
     * Set ophalen_winkel
     *
     * @param string $ophalen_winkel
     * @return null
     */
    public function setOphalenWinkel($ophalen_winkel)
	{
			 return $this->setData(static::COMMENT_FIELD_OP, $ophalen_winkel);	
	
		
		
	}
}
