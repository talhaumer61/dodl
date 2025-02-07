<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();
session_start();

// INSERT COURSE TO WISHLIST
if (isset($_POST["wishlist"])) {
    if(isset($_SESSION['userlogininfo'])){
        // CHECK WISHLIST
		$conWish = array(
                             'select'       =>  'wl_id'
                            ,'where'        =>  array(
                                                        'id_std'    =>   cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                    )
                            ,'return_type'  =>  'count'
                        );        
        // CHECK ENROLLMENT
		$conEnroll = array(
                             'select'       =>  'secs_id'
                            ,'where'        =>  array(
                                                         'id_std'       =>   cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                        ,'is_deleted'   =>  '0'
                                                    )
                            ,'search_by'    =>  ' AND secs_status IN (1,2)'
                            ,'return_type'  =>  'count'
                        );
        // 1 = PROGRAM, 2 = MASTER TRACK, 3 = COURSE, 4 = e-Trainings
        if($_POST['wishlist_type'] == 1){
            $conWish['where']['id_ad_prg']      = cleanvars($_POST["wishlist"]);
            $conEnroll['where']['id_ad_prg']    = cleanvars($_POST["wishlist"]);
        }        
        elseif($_POST['wishlist_type'] == 2){
            $conWish['where']['id_mas']         = cleanvars($_POST["wishlist"]);
            $conEnroll['where']['id_mas']       = cleanvars($_POST["wishlist"]);
        }
        else if($_POST['wishlist_type'] == 3 || $_POST['wishlist_type'] == 4){
            $conWish['where']['id_curs']        = cleanvars($_POST["wishlist"]);
            $conEnroll['where']['id_curs']      = cleanvars($_POST["wishlist"]);
        }


		if ($dblms->getRows(WISHLIST, $conWish) || $dblms->getRows(ENROLLED_COURSES, $conEnroll)) {
            $data = ["msg" => "already"];
            header('Content-Type: application/json');
            echo json_encode($data);
		} else {
            $values = array(
                                 'id_std'   => cleanvars($_SESSION['userlogininfo']['STDID'])     
                                ,'id_type'  => cleanvars($_POST["wishlist_type"])
                            );
                            
            // 1 = PROGRAM, 2 = MASTER TRACK, 3 = COURSE
            if($_POST['wishlist_type'] == 1){
                // GET COURES
                $condition = array(
                                         'select'       =>  'GROUP_CONCAT(id_curs) courses'
                                        ,'where'        =>  array(
                                                                    'id_ad_prg'    =>   cleanvars($_POST["wishlist"])
                                                                )
                                        ,'return_type'  =>  'single'
                                    );
                $PROGRAMS_STUDY_SCHEME = $dblms->getRows(PROGRAMS_STUDY_SCHEME, $condition);
                // ALTER VALUES
                $values['id_curs']      = cleanvars($PROGRAMS_STUDY_SCHEME["courses"]);
                $values['id_ad_prg']    = cleanvars($_POST["wishlist"]);
            }
            elseif($_POST['wishlist_type'] == 2){
                // GET COURES
                $condition = array(
                                         'select'       =>  'GROUP_CONCAT(id_curs) courses'
                                        ,'where'        =>  array(
                                                                    'id_mas'    =>   cleanvars($_POST["wishlist"])
                                                                )
                                        ,'return_type'  =>  'single'
                                    );
                $MASTER_TRACK_DETAIL = $dblms->getRows(MASTER_TRACK_DETAIL, $condition);
                // ALTER VALUES
                $values['id_curs']  = cleanvars($MASTER_TRACK_DETAIL["courses"]);
                $values['id_mas']   = cleanvars($_POST["wishlist"]);
            }
            elseif($_POST['wishlist_type'] == 3 || $_POST['wishlist_type'] == 4){
                $values['id_curs'] = cleanvars($_POST["wishlist"]);
            }

            $sqllms = $dblms->Insert(WISHLIST, $values);
            if ($sqllms) {
                $latestID = $dblms->lastestid();                
                // sendRemark("Added to Wishlist", "1", $latestID);
                $data = ["msg" => "add" , "id" => $latestID];
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
    } else {
        $data = ["msg" => "login"];        
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

// DELETE FROM WISHLIST
if (isset($_POST['remove_wishlist'])) {
    $sql = $dblms->querylms('DELETE FROM '.WISHLIST.' WHERE wl_id = "'.cleanvars($_POST['remove_wishlist']).'"');
    if ($sql) {
        $data = ["msg" => "delete"];        
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    else{
        $data = ["msg" => "error"];        
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
?>