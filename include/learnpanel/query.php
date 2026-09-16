<?php

if (isset($_POST['id_discussion'])) {
    $values = array (
                    "id_std"            =>	cleanvars($_SESSION['userlogininfo']['STDID']),
                    "id_discussion"		=>	cleanvars($_POST['id_discussion']),
                    "dst_detail"        =>	cleanvars($_POST['dst_detail'])
                );
    $sqllms  = $dblms->insert(COURSES_DISCUSSIONSTUDENTS, $values);
    $latestID = $dblms->lastestid();
    if ($sqllms) {
        sendRemark("Diccussion Submitted",'1',$latestID);
    }
}

if (isset($_POST['id_assignment'])) {
    $values = array (
                    "id_std"            =>	cleanvars($_SESSION['userlogininfo']['STDID']),
                    "id_assignment"		=>	cleanvars($_POST['id_assignment']),
                    "student_reply"     =>	cleanvars($_POST['student_reply']),
                    "marks"             =>	'0',
                    "submit_date"       =>	date("Y-m-d"),
                    "id_added"          =>	cleanvars($_SESSION['userlogininfo']['LOGINIDA']),
                    'date_added'        =>  date("Y-m-d G:i:s")
                );
    $sqllms  = $dblms->insert(COURSES_ASSIGNMENTSTUDENTS, $values);
    $latestID = $dblms->lastestid();
    if ($sqllms) {
        if(!empty($_FILES['student_file']['name'])) {
            $path_parts 			= pathinfo($_FILES["student_file"]["name"]);
            $extension 				= strtolower($path_parts['extension']);
            if(in_array($extension , array('pdf','xlsx','xls','doc','docx','ppt','pptx','png','jpg','jpeg','rar','zip'))) {
                $img_dir 			= 'uploads/files/student_assignments/';
                $originalImage		= $img_dir.to_seo_url(cleanvars($_POST['name'])).'-'.$latestID.".".($extension);
                $img_fileName		= to_seo_url(cleanvars($_POST['name'])).'-'.$latestID.".".($extension);
                $dataImage 			= array( 'student_file' => $img_fileName );
                $sqlUpdateImg 		= $dblms->Update(COURSES_ASSIGNMENTSTUDENTS, $dataImage, "WHERE id = '".$latestID."'");
                if ($sqlUpdateImg) {
                    move_uploaded_file($_FILES['student_file']['tmp_name'],$originalImage);
                }
            }
        }
        sendRemark("Student Insert Remarks",'1',$latestID);
    }
}

?>