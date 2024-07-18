<?php
/**
 * paymentReturn.php
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
/*
class PaydunyaPaymentReturnController
{
    private $module;
    private $file;
    private $context;


    public function __construct($module, $file, $path)
    {
        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        $this->_path = $path;
    }

    public function run($params)
    {
        return $this->check_paydunya_response( Tools::getValue('token') );
    }

    private function check_paydunya_response($invoice_token) {

        if (!empty($invoice_token)) {
            
            try {
                $ch = curl_init();
                $master_key = Configuration::get('PAYDUNYA_MASTER_KEY');
                $url = '';
                $token = '';
                $private_key = '';

                if (Configuration::get('PAYDUNYA_MODE') == 'live') {
                    $url = 'https://app.paydunya.com/api/v1/checkout-invoice/confirm/' . $invoice_token;
                    $private_key = Configuration::get('PAYDUNYA_LIVE_PRIVATE_KEY');
                    $token = Configuration::get('PAYDUNYA_LIVE_TOKEN');
                } else {
                    $url = 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/confirm/'  . $invoice_token;
                    $private_key = Configuration::get('PAYDUNYA_TEST_PRIVATE_KEY');
                    $token = Configuration::get('PAYDUNYA_TEST_TOKEN');
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_NOBODY, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "PAYDUNYA-MASTER-KEY: $master_key",
                        "PAYDUNYA-PRIVATE-KEY: $private_key",
                        "PAYDUNYA-TOKEN: $token"
                ));

                $response = curl_exec($ch);
                $response_decoded = json_decode($response);
                $respond_code = $response_decoded->response_code;
                if ($respond_code == "00") {
                    //payment found
                    $status = $response_decoded->status;
                    $custom_data = $response_decoded->custom_data;

                    // Check if cart is valid
                    $cart_id = $custom_data->cart_id;
                    $order_id = $custom_data->order_id;
                    $cart = new Cart((int)$cart_id);
                    if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 ||
                        $cart->id_address_invoice == 0 || !$this->module->active)
                        die('Invalid cart');

                    // Check if customer exists
                    $customer = new Customer((int) $cart->id_customer);
                    if (!Validate::isLoadedObject($customer))
                        die('Invalid customer');

                    $currency = new Currency((int)$cart->id_currency);
                    $total_paid = $response_decoded->invoice->total_amount;

                    if ($status == "completed") {
                        $order_id = Order::getByCartId($cart_id);
                        $objOrder = new Order($order_id);
                        $objOrder->setCurrentState(Configuration::get('PS_OS_PAYMENT'));

                        $this->context->smarty->assign('return_message', Configuration::get('PAYDUNYA_SUCCESS_MESSAGE'));
                        return $this->module->display($this->file, 'payment_return.tpl');


                    } else {
                        $this->context->smarty->assign('return_message', 'Vous recevrez votre facture électronique par mail une fois le paiement effectué.');
                        return $this->module->display($this->file, 'payment_return.tpl');
                    }
                } else {
                    $order_id = (int)Order::getByCartId($cart_id);
                    $objOrder = new Order($order_id);
                    $objOrder->setCurrentState(Configuration::get('PS_OS_ERROR'));
                    $this->context->smarty->assign('return_message', Configuration::get('PAYDUNYA_ERROR_MESSAGE'));
                    return $this->module->display($this->file, 'payment_return.tpl');
                }
            } catch (Exception $e) {
                $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
                $redirect_url = Tools::getShopProtocol().$shop->domain.$shop->getBaseURI();
                $redirect_url .= $return_url.'index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&key='.$cart->secure_key;
                Tools::redirectLink($redirect_url);
                die();
            }
        }
    }
} */


class PaydunyaPaymentReturnController
{
    private $module;
    private $file;
 
    private $context;

    public function __construct($module, $file, $path)
    {
        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        
    }

    public function run($params)
    {
        return $this->check_paydunya_response( Tools::getValue('token') );
    }

    private function check_paydunya_response($invoice_token) {
        if (!empty($invoice_token)) {
            try {
                $ch = curl_init();
                $master_key = Configuration::get('PAYDUNYA_MASTER_KEY');
                $url = '';
                $token = '';
                $private_key = '';
    
                if (Configuration::get('PAYDUNYA_MODE') == 'live') {
                    $url = 'https://app.paydunya.com/api/v1/checkout-invoice/confirm/' . $invoice_token;
                    $private_key = Configuration::get('PAYDUNYA_LIVE_PRIVATE_KEY');
                    $token = Configuration::get('PAYDUNYA_LIVE_TOKEN');
                } else {
                    $url = 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/confirm/' . $invoice_token;
                    $private_key = Configuration::get('PAYDUNYA_TEST_PRIVATE_KEY');
                    $token = Configuration::get('PAYDUNYA_TEST_TOKEN');
                }
    
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_NOBODY, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "PAYDUNYA-MASTER-KEY: $master_key",
                    "PAYDUNYA-PRIVATE-KEY: $private_key",
                    "PAYDUNYA-TOKEN: $token"
                ));
    
                $response = curl_exec($ch);
                $response_decoded = json_decode($response);
                $respond_code = $response_decoded->response_code;
    
                if ($respond_code == "00") {
                    // Payment found
                    $status = $response_decoded->status;
                    $custom_data = $response_decoded->custom_data;
    
                    // Check if cart is valid
                    $cart_id = $custom_data->cart_id;
                    $order_id = $custom_data->order_id;
                    $cart = new Cart((int) $cart_id);
                    if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
                        PrestaShopLogger::addLog('Invalid cart: ' . print_r($cart, true), 3);
                        die('Invalid cart');
                    }
    
                    // Check if customer exists
                    $customer = new Customer((int) $cart->id_customer);
                    if (!Validate::isLoadedObject($customer)) {
                        PrestaShopLogger::addLog('Invalid customer ID: ' . $cart->id_customer, 3);
                        die('Invalid customer');
                    }
    
                    $currency = new Currency((int) $cart->id_currency);
                    $total_paid = $response_decoded->invoice->total_amount;
    
                    if ($status == "completed") {
                        PrestaShopLogger::addLog('Payment completed for cart ID: ' . $cart_id, 1);
    
                        $order_id = Order::getByCartId($cart_id);
                        if (!$order_id) {
                            PrestaShopLogger::addLog('Order not found for cart ID: ' . $cart_id, 3);
                            die('Order not found');
                        }
    
                        $objOrder = new Order($order_id);
                        if (!Validate::isLoadedObject($objOrder)) {
                            PrestaShopLogger::addLog('Invalid order ID: ' . $order_id, 3);
                            die('Invalid order');
                        }
    
                        // Update the order status to Payment accepted
                        $objOrder->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                        PrestaShopLogger::addLog('Order status updated to Payment accepted for order ID: ' . $objOrder->id, 1);
    
                        // Redirect to order confirmation page
                        $this->context->smarty->assign('return_message', Configuration::get('PAYDUNYA_SUCCESS_MESSAGE'));
                        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $cart_id . '&id_module=' . (int) $this->module->id . '&id_order=' . (int) $objOrder->id . '&key=' . $customer->secure_key);
                    } else {
                        PrestaShopLogger::addLog('Payment not completed. Status: ' . $status, 3);
                        $this->context->smarty->assign('return_message', 'Vous recevrez votre facture électronique par mail une fois le paiement effectué.');
                        return $this->module->display($this->file, 'payment_return.tpl');
                    }
                } else {
                    $custom_data = $response_decoded->custom_data;
    
                    // Check if cart is valid
                    $cart_id = $custom_data->cart_id;
                    $order_id = (int) Order::getByCartId($cart_id);
                    $objOrder = new Order($order_id);
                    $objOrder->setCurrentState(Configuration::get('PS_OS_ERROR'));
                    $this->context->smarty->assign('return_message', Configuration::get('PAYDUNYA_ERROR_MESSAGE'));
                    return $this->module->display($this->file, 'payment_return.tpl');
                }
            } catch (Exception $e) {
                PrestaShopLogger::addLog('Exception: ' . $e->getMessage(), 3);
    
                $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
                $redirect_url = Tools::getShopProtocol() . $shop->domain . $shop->getBaseURI();
    
                // If $return_url is not defined, use a default value
                if (!isset($return_url)) {
                    $return_url = '';
                }
    
                // Ensure that $return_url ends with a '/'
                if (substr($return_url, -1) !== '/') {
                    $return_url .= '/';
                }
    
                // Check if cart is defined
                if (!isset($cart)) {
                    die('Cart not defined');
                }
    
                $redirect_url .= $return_url . 'index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&key=' . $cart->secure_key;
                Tools::redirectLink($redirect_url);
                die();
            }
        }
    }
    
}