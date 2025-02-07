<?php


class PayFast_Gateway
{

    private $apiconfig;
    private $merchant_id;
    private $secured_key;

    public function __construct($merchantId, $securedKey)
    {
        $this->merchant_id = $merchantId;
        $this->secured_key = $securedKey;
        $this->_loadApiConfigs();
    }

    public function getPaymentGatewayToken($dataQueryString)
    { //echo $this->apiconfig->token_url;
        $response = $this->_remoteCurlRequest($this->apiconfig->token_url, "POST", $dataQueryString);
        $response_decode = json_decode($response);

        if (isset($response_decode->ACCESS_TOKEN)) {
            return $response_decode->ACCESS_TOKEN;
        }

        return null;
    }

    /**
     * @params $url string API URL
     * @params $method string HTTP method (GET/POST)
     * @params $dataQueryString string Date in query string format
     * 
     */
    private function _remoteCurlRequest($url, $method, $dataQueryString)
    {

        $ch = curl_init();
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataQueryString);
        } elseif ($method == 'GET') {
            $url = sprintf("%s?%s", $url, $dataQueryString);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));
        curl_setopt($ch, CURLOPT_USERAGENT, 'CS CART PayFast Plugin CURL 2.0');
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        error_log('CURL Error: ' . $error_msg);
        curl_close($ch);
        return $response;
    }

    public function getTransactionUrl()
    {
        return  $this->apiconfig->transaction_url;
    }

    private function _loadApiConfigs()
    {
        $xmldata = simplexml_load_file(__DIR__ . "/payfast.xml");
        $this->apiconfig =  $xmldata->apiconfig;
    }
}
