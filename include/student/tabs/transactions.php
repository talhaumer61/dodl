<?php
$conditions = array ( 
    'select' 		=>	'trans_id, trans_no, date, trans_amount, currency_code'
   ,'where' 		=>	array( 
                                'is_deleted'		=> '0'
                               ,'trans_status'      => '1' 
                               ,'id_std' 	        => cleanvars($_SESSION['userlogininfo']['STDID']) 
                           ) 
   ,'return_type'	=>	'all'
); 
$row = $dblms->getRows(TRANSACTION, $conditions);
echo'
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">
            <div class="filter-grp ticket-grp d-flex align-items-center justify-content-between" >
                <h3>Transaction History</h3>
            </div>
            <div class="settings-tickets-blk table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Referred ID</th>
                            <th>Date of Transaction</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>';
                        if($row){
                            foreach ($row as $value) {
                                echo '
                                <tr>
                                    <td><a href="javascript:;">'.$value['trans_no'].'</a></td>
                                    <td>'.date('d M, y',strtotime($value['date'])).'</td>
                                    <td>'.$value['currency_code'].' : '.$value['trans_amount'].'</td>
                                </tr>';
                            } 
                        } else{
                            echo'
                            <tr>
                                <td colspan="3" align="center">No Record Found</td>
                            </tr>';
                        }
                        echo'
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>';
?>