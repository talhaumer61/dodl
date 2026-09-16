<?php

/* * *************************************************************************
 *                                                                          *
 *  This addon has been written by
 *  PayFast Technology Team
 *  
 *  PayFast
 *  https://gopayfast.com
 *     
 * ************************************************************************** */

use Tygh\Registry;

$source = isset($_REQUEST['dispatch']) ? $_REQUEST['dispatch'] : '';

switch (defined('PAYMENT_NOTIFICATION')) {
    case true:
        catchGatewayReponse($mode);
        break;
    default:
        initiateTransactionFlow();
}

function initiateTransactionFlow()
{

    if (!defined('BOOTSTRAP')) {
        die('Access denied');
    }

    $cart = &Tygh::$app['session']['cart'];
    $order_id = isset($cart['processed_order_id'][0]) ? $cart['processed_order_id'][0] : null;

    if (!$order_id) {
        $orderurl = fn_url("orders", "C");
        fn_set_notification("W", __("addons.gopayfast_config_failed_head"), __("addons.gopayfast_config_failed"));
        fn_redirect($orderurl);
        exit;
    }
    require_once __DIR__ . '/payfast_auth.php';

    $processor_data = fn_get_payment_method_data($_REQUEST['payment_id']);


    $merchantId = isset($processor_data['processor_params']['gopayfast_merchant_id']) ? $processor_data['processor_params']['gopayfast_merchant_id'] : '';
    $securedKey = isset($processor_data['processor_params']['gopayfast_secured_key']) ? $processor_data['processor_params']['gopayfast_secured_key'] : '';

    if (!$merchantId || !$securedKey) {
        $orderurl = fn_url("orders", "C");
        fn_redirect($orderurl);
        exit;
    }

    $order_info = fn_get_order_info($order_id);
    $order_currency = $order_info['secondary_currency'];
    $order_total = $order_info['total'];


    $storeId = isset($processor_data['processor_params']['gopayfast_store_id']) ? $processor_data['processor_params']['gopayfast_store_id'] : '';
    $companydata = Registry::get('runtime.company_data');
    $_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;

    $orderStatus = $order_info['status'];
    $unProcessableOrder = $orderStatus == "C" || $orderStatus == "A" || $orderStatus == "P";

    if ($unProcessableOrder) {
        $orderurl = fn_url("orders", "C");
        fn_set_notification("W", __("addons.gopayfast_config_failed_head"), __("addons.gopayfast_config_failed"));
        fn_redirect($orderurl);
        exit;
    }

    $signature = hash('sha256', $order_id . $merchantId . $securedKey);
    $merchantname = $companydata['company'];

    $front_redir_url = fn_url(
        sprintf("payment_notification.update?order_id=%s&redirect=Y&payment=gopayfast", $_order_id),
        'C'
    );

    $ipn_url = fn_url(
        sprintf("payment_notification.update?order_id=%s&&payment=gopayfast", $_order_id),
        'C'
    );

    $tokenParams = [
        'MERCHANT_ID' => $merchantId,
        'SECURED_KEY' => $securedKey,
        'BASKET_ID' => $order_id,
        'TXNAMT' => $order_total,
        'CURRENCY_CODE' => $order_currency
    ];

    $tokenQueryString = http_build_query($tokenParams);

    $payfastGateway = new PayFast_Gateway($merchantId, $securedKey);

    $ipgToken = $payfastGateway->getPaymentGatewayToken($tokenQueryString);
    $transactionUrl = $payfastGateway->getTransactionUrl();

    if (!$ipgToken) {
        fn_set_notification("W", __("addons.gopayfast_payment_failed_head"), __("addons.gopayfast_connect_failed"));
        return;
    }

    $payload = array(
        'MERCHANT_ID' =>  $merchantId,
        'MERCHANT_NAME' => $merchantname,
        'TOKEN' => $ipgToken,
        'PROCCODE' => '00',
        'TXNAMT' => $order_total,
        'CURRENCY_CODE' => $order_currency,
        'CUSTOMER_MOBILE_NO' => $order_info['email'],
        'CUSTOMER_EMAIL_ADDRESS' => $order_info['phone'],
        'SIGNATURE' => $signature,
        'VERSION' => 'CSCART-GOPAYFAST-2.0',
        'TXNDESC' => 'Products purchased from ' . $merchantname,
        'SUCCESS_URL' => urlencode($front_redir_url),
        'FAILURE_URL' => urlencode($front_redir_url),
        'BASKET_ID' => $_order_id,
        'ORDER_DATE' => date('Y-m-d H:i:s', time()),
        'CHECKOUT_URL' => urlencode($ipn_url),
        'CUSTOMER_IPADDRESS' => $_SERVER['REMOTE_ADDR'],
        'MERCHANT_USERAGENT' => $_SERVER['HTTP_USER_AGENT'],
        'STORE_ID' => $storeId,
        'SHIPPING_CUSTOMER_NAME' => $order_info['s_firstname'] . ' ' . $order_info['s_lastname'],
        'SHIPPING_ADDRESS_1' => $order_info['s_address'],
        'SHIPPING_ADDRESS_2' => $order_info['s_address_2'],
        'SHIPPING_ADDRESS_CITY' => $order_info['s_city'],
        'SHIPPING_STATE_PROVINCE' => $order_info['s_state'],
        'SHIPPING_POSTALCODE' => $order_info['s_zipcode'],
        'SHIPPING_METHOD' => '',
        'BILLING_CUSTOMER_NAME' =>  $order_info['b_firstname'] . ' ' . $order_info['b_lastname'],
        'BILLING_ADDRESS_CITY' => $order_info['b_city'],
        'BILLING_ADDRESS_1' => $order_info['b_address'],
        'BILLING_ADDRESS_2' =>  $order_info['b_address_2'],
        'BILLING_STATE_PROVINCE' =>   $order_info['b_state'],
        'BILLING_POSTALCODE' => $order_info['b_zipcode'],
        'MERCHANT_USERAGENT' =>  $_SERVER['HTTP_USER_AGENT']
    );


    $order_data = array(
        'order_id' => $order_id,
        'type' => 'E',
        'data' => AREA,
    );

    db_query("REPLACE INTO ?:order_data ?e", $order_data);
    fn_create_payment_form($transactionUrl, $payload, "PayFast", true);
}

function catchGatewayReponse($mode)
{
    switch ($mode) {
        case "notify":
            executeNotify();
            break;
        case "update":
            executeResponse();
            break;
    }
}

function executeNotify()
{
    $redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : '';
    $orderid = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';


    if ($redirect == "Y") {
        fn_order_placement_routines('route', $orderid, array(), true, AREA);
    } else {
        fn_order_placement_routines('route', $orderid, array(), true, AREA, false);
    }
    fn_clear_cart($cart, true);
}

function executeResponse()
{

    $redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : '';
    $basketid = isset($_REQUEST['basket_id']) ? $_REQUEST['basket_id'] : '';
    $payfast_status_msg = isset($_REQUEST['err_msg']) ? $_REQUEST['err_msg'] : '';
    $payfast_transaction_id = isset($_REQUEST['transaction_id']) ? $_REQUEST['transaction_id'] : '';
    $payfast_statuscode = isset($_REQUEST['err_code']) ? $_REQUEST['err_code'] : '';
    $validation_hash = isset($_REQUEST['validation_hash']) ? $_REQUEST['validation_hash'] : '';
    $instrument = isset($_REQUEST['PaymentName']) ? $_REQUEST['PaymentName'] : '';

    if (!$basketid || !$payfast_transaction_id) {
        fn_set_notification("W", __("addons.gopayfast_payment_failed_head"), __("addons.gopayfast_connect_failed"));
        $default_url = fn_url("index.php", 'C');
        fn_redirect($default_url);
        return;
    }

    $order_id = $basketid;

    $order_info = fn_get_order_info($basketid);


    if (!count($order_info)) {
        $failurl = fn_url("payment_notification.notify?payment=gopayfast&order_id=" . $basketid . "&redirect=" . $redirect, 'C');
        $default_url = fn_url("index.php", 'C');
        fn_redirect($default_url);
        return;
    }

    $integrityCheck = validationCheck($basketid, $order_info['payment_id'], $payfast_statuscode, $validation_hash);

    if (!$integrityCheck) {
        fn_set_notification("W", __("addons.gopayfast_payment_failed_head"), __("addons.gopayfast_connect_failed"));
        $default_url = fn_url("index.php", 'C');
        fn_redirect($default_url);
        return;
    }

    $orderstatus = $order_info['status'];

    if ($orderstatus == "C" || $orderstatus == "A" || $orderstatus == "P") {
        $default_url = fn_url("index.php", 'C');
        fn_set_notification("W", __("addons.gopayfast_payment_failed_head"), __("addons.gopayfast_connect_failed"));
        fn_redirect($default_url);
        return;
    }

    $payfastTxnStatus = $payfast_statuscode  == '000' ? 'Success' : 'Failed';
    $txnText = sprintf("Txn Status: %s - Code: %s - %s", $payfastTxnStatus, $payfast_statuscode, $payfast_status_msg);

    $pp_response = [
        'order_status' => ($payfast_statuscode == '000' ? 'P' : 'F'),
        'reason_text' => $txnText,
        'transaction_id' => $payfast_transaction_id,
        'payment_type' => $instrument
    ];
    
    fn_finish_payment($order_id, $pp_response, false);

    if ($redirect == "Y") {
        fn_order_placement_routines('route', $order_id, array(), true, AREA);
        fn_payments_set_company_id($order_id);
        fn_clear_cart($cart, true);
    } else {
        fn_order_placement_routines('route', $order_id, array(), true, AREA, false);
        fn_payments_set_company_id($order_id);
        fn_clear_cart($cart, true);
        echo "Order Updated";
        die();
    }
}

function validationCheck($order_id, $payment_id, $erroCode, $incomingHash)
{

    $processor_data = fn_get_payment_method_data($payment_id);
    $merchantId = isset($processor_data['processor_params']['gopayfast_merchant_id']) ? $processor_data['processor_params']['gopayfast_merchant_id'] : '';
    $securedKey = isset($processor_data['processor_params']['gopayfast_secured_key']) ? $processor_data['processor_params']['gopayfast_secured_key'] : '';

    $hashText = sprintf("%s|%s|%s|%s", $order_id, $securedKey, $merchantId, $erroCode);

    $hash = hash('sha256', $hashText);
    return $hash == $incomingHash;
}
