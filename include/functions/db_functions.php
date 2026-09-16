<?php
class main {

// get single QR COde
    function get_qrchallan($consumerNumber, $consumerName, $amount, $currency, $expiryDateTime) {
        $dblms = new dblms();
        $conditions = array (
                                      'select' 		=> 'id, qrcode, orderid, expirydatetime, status'
                                    , 'where' 		=> array (
                                                                      'consumerno'      => cleanvars($consumerNumber)
                                                                    , 'amount'          => cleanvars($amount)
                                                                    , 'currency'        => cleanvars($currency)
                                                                    , 'expirydatetime'  => date('Y-m-d H:i:00', strtotime($expiryDateTime))
                                                             )
                                    , 'search_by'   => " AND consumername LIKE '".($consumerName)."'"
                                    , 'limit' 	    => 1
                                    , 'return_type' => 'single'
                            );
        $result = $dblms->getRows("cms_qrchallans", $conditions);
        return $result;
    }
// end get single  QR COde

}
// end class 