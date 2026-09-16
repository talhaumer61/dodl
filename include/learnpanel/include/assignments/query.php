<?php
if (isset($_POST['submit_assignment'])) {
    $values = array (
                         "id_std"           => cleanvars($_SESSION['userlogininfo']['STDID'])
                        ,"id_assignment"	=> cleanvars($_POST['id_assignment'])
                        ,"id_curs"	        => cleanvars($_POST['id_curs'])
                        ,"id_mas"	        => cleanvars($_POST['id_mas'])
                        ,"id_ad_prg"	    => cleanvars($_POST['id_ad_prg'])
                        ,"student_reply"    => cleanvars($_POST['student_reply'])
                        ,"submit_date"      => date("Y-m-d")
                        ,"id_added"         => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                        ,'date_added'       => date("Y-m-d G:i:s")
    );
    $sqllms  = $dblms->insert(COURSES_ASSIGNMENTS_STUDENTS, $values);
    if ($sqllms) {
        if(!empty($_FILES['student_file']['name'])) {
            $latestID               = $dblms->lastestid();
            $path_parts 			= pathinfo($_FILES["student_file"]["name"]);
            $extension 				= strtolower($path_parts['extension']);
            if(in_array($extension , array('pdf','xlsx','xls','doc','docx','ppt','pptx','png','jpg','jpeg','rar','zip'))) {
                $img_dir 			= 'uploads/files/student_assignments/';
                $img_fileName		= 'assignment'.'-'.cleanvars($latestID).'-'.cleanvars($_SESSION['userlogininfo']['STDID']).'.'.($extension);
                $originalImage		= $img_dir.$img_fileName;
                $dataImage 			= array( 'student_file' => $img_fileName );
                $sqlUpdateImg 		= $dblms->Update(COURSES_ASSIGNMENTS_STUDENTS, $dataImage, "WHERE id = '".$latestID."'");
                if ($sqlUpdateImg) {
                    move_uploaded_file($_FILES['student_file']['tmp_name'],$originalImage);
                }
            }
        }


        $values = array(
                             'track_status'     => 1
                            ,'is_completed'     => 2
                            ,'track_mood'       => 'assignment'
                            ,'id_std'		    => $_SESSION['userlogininfo']['STDID']
                            ,'id_week'	        => $_POST['id_week']
                            ,"id_assignment"    => cleanvars($_POST['id_assignment'])
                            ,'id_curs'	        => $_POST['id_curs']
                            ,'id_mas'	        => $_POST['id_mas']
                            ,'id_ad_prg'        => $_POST['id_ad_prg']
                            ,'id_added'		    => $_SESSION['userlogininfo']['LOGINIDA']
                            ,'date_added'	    => date('Y-m-d G:i:s')
        ); 
        $sqllms = $dblms->insert(LECTURE_TRACKING, $values);
        sendRemark('Assignment Submitted', '1', $latestID);
        sessionMsg('Successfully', 'Record Successfully Added.', 'success');
        header("Location: ".SITE_URL.CONTROLER."/".$zone."/".$view."/".$slug."/".$flag."", true, 301);
        exit();
    }
}
?>