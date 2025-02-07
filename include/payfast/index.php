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
    function getAccessToken($merchant_id, $secured_key, $basket_id, $trans_amount, $tokenApiUrl)  {
    
        $urlPostParams = sprintf(
                //'MERCHANT_ID=%s&SECURED_KEY=%s',
                 'MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s',
                $merchant_id,
                $secured_key,
              $trans_amount,
              $basket_id
            );
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $tokenApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPostParams);
            curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP PayFast Example');
            $response = curl_exec($ch);
            curl_close($ch);
            $payload = json_decode($response);
            $token = isset($payload->ACCESS_TOKEN) ? $payload->ACCESS_TOKEN : '';
            return $token;
    }
	$token = getAccessToken(PAYFAST_MERCHANTID, PAYFAST_SECUREDKEY, ($_POST['challanNo']), ($_POST['challanAmnt']), PAYFAST_TOKENURL);
    echo '
    <!DOCTYPE html>
    <html>
        <head>
            <link href="https://fonts.googleapis.com/css?family=Titillium+Web&display=swap" rel="stylesheet">
            <style type="text/css">
                body {
                    background: #595BD4;
                    font-family: \'Titillium Web\', sans-serif;
                }
                .loading {
                    position: absolute;
                    left: 0;
                    right: 0;
                    top: 40%;
                    width: 100px;
                    color: #FFF;
                    margin: auto;
                    -webkit-transform: translateY(-50%);
                    -moz-transform: translateY(-50%);
                    -o-transform: translateY(-50%);
                    transform: translateY(-50%);
                }
                .loading span {
                    position: absolute;
                    height: 10px;
                    width: 84px;
                    top: 50px;
                    overflow: hidden;
                }
                .loading span > i {
                    position: absolute;
                    height: 4px;
                    width: 4px;
                    border-radius: 50%;
                    -webkit-animation: wait 4s infinite;
                    -moz-animation: wait 4s infinite;
                    -o-animation: wait 4s infinite;
                    animation: wait 4s infinite;
                }
                .loading span > i:nth-of-type(1) {
                    left: -28px;
                    background: yellow;
                }
                .loading span > i:nth-of-type(2) {
                    left: -21px;
                    -webkit-animation-delay: 0.8s;
                    animation-delay: 0.8s;
                    background: lightgreen;
                }

                @-webkit-keyframes wait {
                    0%   { left: -7px  }
                    30%  { left: 52px  }
                    60%  { left: 22px  }
                    100% { left: 100px }
                }
                @-moz-keyframes wait {
                    0%   { left: -7px  }
                    30%  { left: 52px  }
                    60%  { left: 22px  }
                    100% { left: 100px }
                }
                @-o-keyframes wait {
                    0%   { left: -7px  }
                    30%  { left: 52px  }
                    60%  { left: 22px  }
                    100% { left: 100px }
                }
                @keyframes wait {
                    0%   { left: -7px  }
                    30%  { left: 52px  }
                    60%  { left: 22px  }
                    100% { left: 100px }
                }
            </style>
        </head>
        <body>
            <div class="loading">
                <p>Please wait</p>
                <span><i></i><i></i></span>
            </div>
    
            <form name="MerchantRequest" id="MerchantRequest" method="POST" action= "'.PAYFAST_TRNSURL.'" id="myForm" >    
                <input type="hidden" name="CURRENCY_CODE" value="'.$_POST['currency_code'].'">
                <input type="hidden" name="MERCHANT_ID" value="'.PAYFAST_MERCHANTID.'">
                <input type="hidden" name="MERCHANT_NAME" value="'.SITE_NAME.'">
                <input type="hidden" name="TOKEN" value="'.$token.'">
                <input type="hidden" name="SUCCESS_URL" value="'.SITE_URL.'payfast-success/'.$_POST['challanId'].'/'.$_POST['CustomerId'].'/">
                <input type="hidden" name="FAILURE_URL" value="'.SITE_URL.'payfast-cancel/'.$_POST['challanId'].'/'.$_POST['CustomerId'].'/">
                <input type="hidden" name="CHECKOUT_URL" value="'.SITE_URL.'payfast-success/'.$_POST['challanId'].'/'.$_POST['CustomerId'].'/">
                <input type="hidden" name="CUSTOMER_EMAIL_ADDRESS" value="'.$_POST['CustomerEmail'].'">
                <input type="hidden" name="CUSTOMER_MOBILE_NO" value="'.$_POST['CustomerMobile'].'">
                <input type="hidden" name="TXNAMT" value="'.$_POST['challanAmnt'].'">
                <input type="hidden" name="BASKET_ID" value="'.$_POST['challanNo'].'">
                <input type="hidden" name="ORDER_DATE" value="'.date('Y-m-d G:i:s', time()).'">
                <input type="hidden" name="SIGNATURE" value="">
                <input type="hidden" name="VERSION" value="MY_VER_1.0">
                <input type="hidden" name="TXNDESC" value="'.SITE_NAME.' - Course Fee">
                <input type="hidden" name="PROCCODE" value="00">
                <input type="hidden" name="TRAN_TYPE" value="ECOMM_PURCHASE">
                <input type="hidden" name="STORE_ID" value="" >
                <input type="SUBMIT" value="SUBMIT" style="display:none;">
            </form>
            <script type="text/javascript">
                document.getElementById("MerchantRequest").submit(); // Here MerchantRequest is the id of your form
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