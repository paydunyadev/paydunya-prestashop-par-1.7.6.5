<?php
/**
 * payment.php
 *
 * Copyright (c) 2017 PayDunya
 *
 * LICENSE:
 *
 * This payment module is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation; either version 3 of the License, or (at
 * your option) any later version.
 *
 * This payment module is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public
 * License for more details.
 *
 * @copyright 2016 PayDunya
 * @license   http://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://paydunya.com
 */


class PaydunyaValidationIPNModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        try {
            if($_POST['data']['hash'] === hash('sha512', Configuration::get('PAYDUNYA_MASTER_KEY'))) {
                $response_decoded = $_POST['data'];

                if ($response_decoded['response_code'] == "00") {
                    //payment found
                    $status = $response_decoded['status'];
                    $custom_data = $response_decoded['custom_data'];

                    // Check if cart is valid
                    $cart_id = $custom_data['cart_id'];
                    $order_id = $custom_data['order_id'];
                    $cart = new Cart((int)$cart_id);
                    if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 ||
                        $cart->id_address_invoice == 0 || !$this->module->active)
                        die('Invalid cart');

                    // Check if customer exists
                    $customer = new Customer($cart->id_customer);
                    if (!Validate::isLoadedObject($customer))
                        die('Invalid customer');

                    $currency = new Currency((int)$cart->id_currency);
                    $total_paid = $response_decoded['invoice']['total_amount'];

                    if ($status == "completed") {
                        $order_id = (int)Order::getOrderByCartId($cart_id);
                        $objOrder = new Order($order_id);
                        $objOrder->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                    }
                } else {
                    $order_id = (int)Order::getOrderByCartId($cart_id);
                    $objOrder = new Order($order_id);
                    $objOrder->setCurrentState(Configuration::get('PS_OS_ERROR'));
                }
            }
        } catch(Exception $e) {
            die();
        }
    }
}