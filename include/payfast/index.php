<?php
//Require Vars, DB Connection and Function Files
require_once ("include/dbsetting/lms_vars_config.php");
require_once('include/dbsetting/classdbconection.php');
$dblms = new dblms();
require_once('include/functions/login_func.php');
require_once('include/functions/functions.php');

//User Authentication
checkCpanelLMSSTDLogin();

//submit_converttojoborder
if(isset($_POST['submit_paynow'])) {
    
    function getAccessToken($merchant_id, $secured_key, $basket_id, $trans_amount, $currency_code, $tokenApiUrl)  {
        // Ensure required parameters are set
        if (empty($merchant_id) || empty($secured_key) || empty($basket_id) || empty($trans_amount) || empty($currency_code)) {
            die("Error: Required parameters are missing!");
        }
        
        $urlPostParams = http_build_query([
            'MERCHANT_ID' => $merchant_id,
            'SECURED_KEY' => $secured_key,
            'TXNAMT' => $trans_amount,
            'BASKET_ID' => $basket_id,
            'CURRENCY_CODE' => $currency_code
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPostParams);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        
        // Decode response
        $payload = json_decode($response, true);
        
        // Check for errors
        if (isset($payload['errorCode'])) {
            die("API Error: ".$payload['errorDescription']);
        }

        return $payload['ACCESS_TOKEN'] ?? '';
    }

    // Get token
    $token = getAccessToken(
        PAYFAST_MERCHANTID, 
        PAYFAST_SECUREDKEY, 
        $_POST['challanNo'], 
        number_format((float)$_POST['challanAmnt'], 2, '.', ''), 
        $_POST['currency_code'], 
        PAYFAST_TOKENURL
    );

    if (empty($token)) {
        die("Error: Failed to retrieve access token!");
    }

    echo '
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <!-- <meta http-equiv="refresh" content="0;url='.PAYFAST_TRNSURL.'"> -->
            <link href="https://fonts.googleapis.com/css?family=Titillium+Web&display=swap" rel="stylesheet">
            <style type="text/css">
                body { background: #595BD4; font-family: "Titillium Web", sans-serif; }
                .loading { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); color: #FFF; text-align: center; }
                .loading p { font-size: 18px; }
            </style>
        </head>
        <body>
            <div class="loading">
                <p>Please wait... Redirecting to payment gateway</p>
            </div>
            <form id="MerchantRequest" method="POST" action="'.PAYFAST_TRNSURL.'">    
                <input type="hidden" name="CURRENCY_CODE" value="'.htmlspecialchars($_POST['currency_code']).'">
                <input type="hidden" name="MERCHANT_ID" value="'.PAYFAST_MERCHANTID.'">
                <input type="hidden" name="MERCHANT_NAME" value="Minhaj University Lahore">
                <input type="hidden" name="TOKEN" value="'.htmlspecialchars($token).'">
                <input type="hidden" name="SUCCESS_URL" value="'.SITE_URL.'payfast-success/'.htmlspecialchars($_POST['challanId']).'/'.htmlspecialchars($_POST['CustomerId']).'/">
                <input type="hidden" name="CHECKOUT_URL" value="'.SITE_URL.'payfast-success/'.htmlspecialchars($_POST['challanId']).'/'.htmlspecialchars($_POST['CustomerId']).'/">
                <input type="hidden" name="FAILURE_URL" value="'.SITE_URL.'payfast-cancel/'.htmlspecialchars($_POST['challanId']).'/'.htmlspecialchars($_POST['CustomerId']).'/">
                <input type="hidden" name="CUSTOMER_EMAIL_ADDRESS" value="'.htmlspecialchars($_POST['CustomerEmail']).'">
                <input type="hidden" name="CUSTOMER_MOBILE_NO" value="'.(empty($_POST['CustomerMobile']) ? '923154003459' : $_POST['CustomerMobile']).'">
                <input type="hidden" name="TXNAMT" value="'.htmlspecialchars(number_format((float)$_POST['challanAmnt'], 2, '.', '')).'">
                <input type="hidden" name="BASKET_ID" value="'.htmlspecialchars($_POST['challanNo']).'">
                <input type="hidden" name="ORDER_DATE" value="'.date('Y-m-d H:i:s').'">
                <input type="hidden" name="TXNDESC" value="'.SITE_NAME.' - Course Fee">
                <input type="hidden" name="TRAN_TYPE" value="ECOMM_PURCHASE">
                <input type="hidden" name="VERSION" value="MY_VER_1.0">
            </form>
            <script type="text/javascript">
                document.getElementById("MerchantRequest").submit();
            </script>
        </body>
    </html>';
    exit();

} else {
    sessionMsg("Error!","Something went wrong...!","danger");
    header("Location: ".SITE_URL."student/challans/", true, 301);
    exit();
}
?>