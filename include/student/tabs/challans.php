<?php
$conditions = array ( 
                         'select' 		=>	'ch.*'
                        ,'where' 		=>	array( 
                                                     'ch.is_deleted'    => '0'
                                                    ,'ch.id_std'        => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                ) 
                        ,'order_by'     =>	'ch.challan_id DESC'
                        ,'return_type'	=>	'all'
                        ); 
$row = $dblms->getRows(CHALLANS.' ch', $conditions);
echo'
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">
            <div class="filter-grp ticket-grp d-flex align-items-center justify-content-between" >
                <h3>Challans</h3>
            </div>
            <div class="settings-tickets-blk table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">Sr.</th>
                            <th>Challan No</th>
                            <th width="100" class="text-center">Issue Date</th>
                            <th width="100" class="text-center">Due Date</th>
                            <th width="100" class="text-center">Amount</th>
                            <th width="100" class="text-center">Paid</th>
                            <th width="100" class="text-center">Paid Date</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="100" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>';
                        if($row){
                            $srno = 0;
                            foreach ($row as $value) {
                                $srno++;
                                echo '
                                <tr>
                                    <td class="text-center">'.$srno.'</td>
                                    <td>'.$value['challan_no'].'</td>
                                    <td class="text-center">'.date('d M, Y',strtotime($value['issue_date'])).'</td>
                                    <td class="text-center">'.date('d M, Y',strtotime($value['due_date'])).'</td>
                                    <td class="text-center">'.$value['currency_code'].' : '.$value['total_amount'].'</td>
                                    <td class="text-center">'.$value['currency_code'].' : '.$value['paid_amount'].'</td>
                                    <td class="text-center">'.date('d M, Y',strtotime($value['paid_date'])).'</td>
                                    <td class="text-center">'.get_payments($value['status']).'</td>
                                    <td class="text-center">';
                                        // if($value['status'] == '2'){
                                        //     echo'<a class="btn btn-sm btn-secondary" href="'.SITE_URL.'challan-print/'.$value['challan_no'].'" target="_blank"><i class="feather-printer"></i> Print</a>';
                                        // }
                                        if($value['status'] == '2'){
                                            echo $payNow = '<form class="mt-1" action="'.SITE_URL.'payfast" method="POST">
                                                <input type="hidden" name="challanId" id="challanId" value="'.$value['challan_id'].'">
                                                <input type="hidden" name="challanNo" id="challanNo" value="'.$value['challan_no'].'">
                                                <input type="hidden" name="currency_code" id="currency_code" value="'.$value['currency_code'].'">
                                                <input type="hidden" name="challanAmnt" id="challanAmnt" value="'.$value['total_amount'].'">
                                                <input type="hidden" name="CustomerName" id="CustomerName" value="'.$_SESSION['userlogininfo']['LOGINNAME'].'">
                                                <input type="hidden" name="CustomerMobile" id="CustomerMobile" value="'.$_SESSION['userlogininfo']['LOGINPHONE'].'">
                                                <input type="hidden" name="CustomerEmail" id="CustomerEmail" value="'.$_SESSION['userlogininfo']['LOGINEMAIL'].'">
                                                <input type="hidden" name="CustomerId" id="CustomerId" value="'.$_SESSION['userlogininfo']['STDID'].'">
                                                <button class="btn btn-secondary btn-sm" type="submit" name="submit_paynow" id="submit_paynow"><i class="fa fa-credit-card"></i> Pay Now</button>
                                            </form>';
                                        }
                                        echo'
                                    </td>
                                </tr>';
                            } 
                        }
                        else{
                            echo '
                            <tr>
                                <td colspan="3" align="center">No Record Found</td>
                            </tr>';
                        }
                        echo '
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>';
?>