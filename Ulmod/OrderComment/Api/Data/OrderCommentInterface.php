<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ulmod\OrderComment\Api\Data;

/**
 * Interface OrderCommentInterface
 * @api
 */
interface OrderCommentInterface
{
    /**
     * Get comment
     *
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return null
     */
    public function setComment($comment);
	 /**
     * Get customer_shipping_comments
     *
     * @return string|null
     */
    public function getCustomerShippingComments();

    /**
     * Set customer_shipping_comments
     *
     * @param string $customer_shipping_comments
     * @return null
     */
    public function setCustomerShippingComments($customer_shipping_comments);
	 /**
     * Get customer_shipping_date
     *
     * @return string|null
     */
    public function getCustomerShippingDate();

    /**
     * Set customer_shipping_date
     *
     * @param string $customer_shipping_date
     * @return null
     */
    public function setCustomerShippingDate($customer_shipping_date);
	 /**
     * Get ophalen_winkel
     *
     * @return string|null
     */
    public function getOphalenWinkel();

    /**
     * Set ophalen_winkel
     *
     * @param string $ophalen_winkel
     * @return null
     */
    public function setOphalenWinkel($ophalen_winkel);
}
