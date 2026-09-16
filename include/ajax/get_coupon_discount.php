<?php
include "../dbsetting/lms_vars_config.php";
include "../dbsetting/classdbconection.php";
$dblms = new dblms();
include "../functions/functions.php";

$today = date('Y-m-d');
$condition = array ( 
                         'select'       =>	'cpn_code, cpn_type, cpn_percent_amount'
                        ,'where'        =>	array( 
                                                     'is_deleted'       =>  0
                                                    ,'is_applied'       =>  2
                                                    ,'cpn_status'       =>  1
                                                    ,'BINARY cpn_code'  =>  cleanvars($_POST['cpn_code'])
                                                )
                        ,'search_by'    =>  ' AND cpn_start_date <= "'.$today.'" AND cpn_end_date >= "'.$today.'"'
                        ,'return_type'	=>	'single'
                    );
$row = $dblms->getRows(COUPONS, $condition);
if ($row) {
    if($row['cpn_type'] == 1){
        $discount = (($row['cpn_percent_amount'] / 100) * $_POST['total_amount']);
        $amount = $_POST['total_amount'] - $discount;
    } else if($row['cpn_type'] == 2){
        $discount = $row['cpn_percent_amount'];
        $amount = $_POST['total_amount'] - $discount;
    }
    $html = '
    <tr><th colspan="4" class="p-0"></th></tr>
    <tr>
        <th></th>
        <th class="text-end">Discount:</th>
        <th><input type="text" class="form-control" name="discount" id="discount" value="'.$discount.'" required readonly></th>
        <th></th>
    </tr>
    <tr>
        <th></th>
        <th class="text-end">After Discount:</th>
        <th><input type="text" class="form-control" name="after_discount_amount" id="after_discount_amount" value="'.$amount.'" required readonly></th>
        <th></th>
    </tr>';
    echo json_encode([
        'coupon_check'  => '<i class="fa fa-check text-success"></i><input type="hidden" class="form-control" name="is_applied" id="is_applied" value="1" required readonly>',
        'html_content'  => $html,
    ]);
} else {
    echo json_encode([
        'coupon_check'  => '<i class="fa fa-close text-danger"></i>',
        'html_content'  => "",
    ]);
}
?>

