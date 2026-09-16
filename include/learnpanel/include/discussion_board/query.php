<?php
if (isset($_POST['submit_discussion'])) {
    $values = array (
                         "id_std"           => cleanvars($_SESSION['userlogininfo']['STDID'])
                        ,"id_discussion"	=> cleanvars($_POST['id_discussion'])
                        ,"dst_detail"       => cleanvars($_POST['dst_detail'])
    );
    $sqllms  = $dblms->insert(COURSES_DISCUSSIONSTUDENTS, $values);
    if ($sqllms) {
        $latestID = $dblms->lastestid();        
        sendRemark('Discussion Borad Added', '1', $latestID);
        sessionMsg('Successfully', 'Record Successfully Added.', 'success');
        header("Location: ".SITE_URL.CONTROLER."/".$zone."/".$view."/".$slug."/".$flag."", true, 301);
        exit();
    }
}
?>