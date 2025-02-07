<?php
if(ZONE == 'page-1'){
    if(!isset($_SESSION['teacher_interest_id'])){
        require_once 'include/head.php';
        require_once 'include/top_nav.php';
        require_once 'include/breadcrumb.php';
        include_once 'query.php';
        echo'
        <section class="course-content">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 mb-2 col-md-12">
                        <form action="" method="post">
                            <div class="settings-widget">
                                <div class="settings-inner-blk p-0">
                                    <div class="sell-course-head comman-space text-center">
                                        <h3>Performa for Course Development Interest</h3>
                                    </div>
                                    <div class="comman-space pb-0">
                                        <div class="settings-tickets-blk course-instruct-blk table-responsive">
                                            <table class="table table-nowrap mb-3">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" class="text-center">Section 1: Personal Information</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th>Full Name <span class="text-danger">*</span>
                                                            <input type="text" class="form-control" name="fullname" required/>
                                                        </td>
                                                        <th>Department <span class="text-danger">*</span>
                                                            <input type="text" class="form-control" name="department" required/>
                                                        </td>
                                                        <th>Designation <span class="text-danger">*</span>
                                                            <input type="text" class="form-control" name="designation" required/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3">Organization/Institute <span class="text-danger">*</span>
                                                            <input type="text" class="form-control" name="organization" required/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Teaching Experience (Years) <span class="text-danger">*</span>
                                                            <input type="number" class="form-control" name="teaching_experience" required/>
                                                        </td>
                                                        <th>Professional Experience (Years) <span class="text-danger">*</span>
                                                            <input type="number" class="form-control" name="professional_experience" required/>
                                                        </td>
                                                        <th>Email <span class="text-danger">*</span>
                                                            <input type="email" class="form-control" name="email" required/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Phone No <span class="text-danger">*</span>
                                                            <input type="text" class="form-control" name="phone_no" required/>
                                                        </td>
                                                        <th>LinkedIn Profile
                                                            <input type="text" class="form-control" name="linkedin_link"/>
                                                        </td>
                                                        <th>Youtube Chanel/Video Link
                                                            <input type="text" class="form-control" name="youtube_link"/>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>';
                                            $queNo=0;
                                            foreach (get_teacher_interest_section() as $key => $value) {
                                                if(in_array($key, [1,2,3])){
                                                    // ENGAGEMENT QUESTIONS
                                                    $conditions = array ( 
                                                                            'select'        =>	'id, type, id_section, question, options'
                                                                            ,'where'        =>	array( 
                                                                                                    'status'       => 1
                                                                                                    ,'is_deleted'   => 0
                                                                                                    ,'id_section'   => cleanvars($key)
                                                                                                )
                                                                            ,'return_type'	=>	'all'
                                                                        );
                                                    $TEACHER_INTEREST_QUESTIONS = $dblms->getRows(TEACHER_INTEREST_QUESTIONS, $conditions);
                                                    if($TEACHER_INTEREST_QUESTIONS){
                                                        $sr=0;
                                                        echo'
                                                        <table class="table table-nowrap mb-3">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5" class="text-center">Section '.$TEACHER_INTEREST_QUESTIONS[0]['id_section'].': '.get_teacher_interest_section($TEACHER_INTEREST_QUESTIONS[0]['id_section']).'</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                                foreach ($TEACHER_INTEREST_QUESTIONS as $keyQue => $valRow) {
                                                                    $sr++;
                                                                    $queNo++;
                                                                    $keyOpt = '';
                                                                    echo'
                                                                    <tr>
                                                                        <th colspan="5">
                                                                            '.$sr.'. '.$valRow['question'].'
                                                                            <input type="hidden" id="question['.$queNo.']" name="question['.$queNo.']" required value="'.$valRow['id'].'">
                                                                            <input type="hidden" id="type['.$queNo.']" name="type['.$queNo.']" required value="'.$valRow['type'].'">
                                                                        </th>
                                                                    </tr>';
                                                                    if($valRow['type'] == 3){
                                                                        echo'
                                                                        <tr>';
                                                                            $options = json_decode(html_entity_decode($valRow['options']), true);
                                                                            foreach ($options as $keyOpt => $valOpt) {
                                                                                echo'
                                                                                <td width="20%">';
                                                                                    if(!empty($valOpt)){                                                                                    
                                                                                        echo'
                                                                                        <div class="form-switch">
                                                                                            <input class="form-check-input" id="option['.$queNo.']" name="option['.$queNo.']" required value="'.$keyOpt.'" type="radio">
                                                                                            <label class="form-check-label mb-0">'.$valOpt.'</label>
                                                                                        </div>';
                                                                                    }
                                                                                    echo'
                                                                                </td>';
                                                                            }
                                                                            echo'
                                                                        </tr>';
                                                                        if($keyOpt == '5' && $valOpt != ''){
                                                                            echo'
                                                                            <tr>
                                                                                <td colspan="5">
                                                                                    <textarea class="form-control" name="answer['.$queNo.']" placeholder="In case of other specify..."></textarea>
                                                                                </td>
                                                                            </tr>';
                                                                        }
                                                                    } else {
                                                                        echo'
                                                                        <tr>
                                                                            <td colspan="5">
                                                                                <textarea class="form-control" name="answer['.$queNo.']"></textarea>
                                                                            </td>
                                                                        </tr>';
                                                                    }
                                                                }
                                                                echo'
                                                            </tbody>
                                                        </table>';
                                                    }
                                                }
                                            }
                                            echo'
                                        </div>
                                    </div>
                                    <div class="sell-course-head comman-space">
                                        <p>Please submit your details by clicking the submit button below. Thank you for your
                                            time and valuable insights!</p>
                                        <p>Thank you again for your participation!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid">
                                <div class="update-profile row">
                                    <div class="col">
                                        <a href="'.SITE_URL.'" class="btn btn-wish w-100 mr-1"><i
                                                class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                                    </div>
                                    <div class="col">
                                        <button name="submit_interest_1" type="submit"
                                            class="btn btn-enroll w-100">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>';
        require_once 'include/footer.php';
    } else{        
        header("Location: ".SITE_URL.CONTROLER."/page-2");
    }
} else if (ZONE == 'page-2') {
    if(isset($_SESSION['teacher_interest_id'])){
        require_once 'include/head.php';
        require_once 'include/top_nav.php';
        require_once 'include/breadcrumb.php';
        include_once 'query.php';
        echo'
        <section class="course-content">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 mb-2 col-md-12">
                        <form action="" method="post">
                            <div class="settings-widget">
                                <div class="settings-inner-blk p-0">
                                    <div class="sell-course-head comman-space text-center">
                                        <h3>Performa for Course Development Interest</h3>
                                    </div>
                                    <div class="comman-space pb-0">
                                        <div class="settings-tickets-blk course-instruct-blk table-responsive">';
                                            $queNo=0;
                                            foreach (get_teacher_interest_section() as $key => $value) {
                                                if(in_array($key, [4,5,6,7])){
                                                    // ENGAGEMENT QUESTIONS
                                                    $conditions = array ( 
                                                                            'select'        =>	'id, type, id_section, question, options'
                                                                            ,'where'        =>	array( 
                                                                                                    'status'       => 1
                                                                                                    ,'is_deleted'   => 0
                                                                                                    ,'id_section'   => cleanvars($key)
                                                                                                )
                                                                            ,'return_type'	=>	'all'
                                                                        );
                                                    $TEACHER_INTEREST_QUESTIONS = $dblms->getRows(TEACHER_INTEREST_QUESTIONS, $conditions);
                                                    if($TEACHER_INTEREST_QUESTIONS){
                                                        $sr=0;
                                                        echo'
                                                        <table class="table table-nowrap mb-3">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5" class="text-center">Section '.$TEACHER_INTEREST_QUESTIONS[0]['id_section'].': '.get_teacher_interest_section($TEACHER_INTEREST_QUESTIONS[0]['id_section']).'</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                                foreach ($TEACHER_INTEREST_QUESTIONS as $keyQue => $valRow) {
                                                                    $sr++;
                                                                    $queNo++;
                                                                    $keyOpt = '';
                                                                    echo'
                                                                    <tr>
                                                                        <th colspan="5">
                                                                            '.$sr.'. '.$valRow['question'].'
                                                                            <input type="hidden" id="question['.$queNo.']" name="question['.$queNo.']" required value="'.$valRow['id'].'">
                                                                            <input type="hidden" id="type['.$queNo.']" name="type['.$queNo.']" required value="'.$valRow['type'].'">
                                                                        </th>
                                                                    </tr>';
                                                                    if($valRow['type'] == 3){
                                                                        echo'
                                                                        <tr>';
                                                                            $options = json_decode(html_entity_decode($valRow['options']), true);
                                                                            foreach ($options as $keyOpt => $valOpt) {
                                                                                echo'
                                                                                <td width="20%">';
                                                                                    if(!empty($valOpt)){                                                                                    
                                                                                        echo'
                                                                                        <div class="form-switch">
                                                                                            <input class="form-check-input" id="option['.$queNo.']" name="option['.$queNo.']" required value="'.$keyOpt.'" type="radio">
                                                                                            <label class="form-check-label mb-0">'.$valOpt.'</label>
                                                                                        </div>';
                                                                                    }
                                                                                    echo'
                                                                                </td>';
                                                                            }
                                                                            echo'
                                                                        </tr>';
                                                                        if($keyOpt == '5' && $valOpt != ''){
                                                                            echo'
                                                                            <tr>
                                                                                <td colspan="5">
                                                                                    <textarea class="form-control" name="answer['.$queNo.']" placeholder="In case of other specify..."></textarea>
                                                                                </td>
                                                                            </tr>';
                                                                        }
                                                                    } else {
                                                                        echo'
                                                                        <tr>
                                                                            <td colspan="5">
                                                                                <textarea class="form-control" name="answer['.$queNo.']"></textarea>
                                                                            </td>
                                                                        </tr>';
                                                                    }
                                                                }
                                                                echo'
                                                            </tbody>
                                                        </table>';
                                                    }
                                                }
                                            }
                                            echo'
                                        </div>
                                    </div>
                                    <div class="sell-course-head comman-space">
                                        <p>Please submit your details by clicking the submit button below. Thank you for your
                                            time and valuable insights!</p>
                                        <p>Thank you again for your participation!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid">
                                <div class="update-profile row">
                                    <div class="col">
                                        <a href="'.SITE_URL.'" class="btn btn-wish w-100 mr-1"><i
                                                class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                                    </div>
                                    <div class="col">
                                        <button name="submit_interest_2" type="submit"
                                            class="btn btn-enroll w-100">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>';    
        require_once 'include/footer.php';
    } else {        
        header("Location: ".SITE_URL.CONTROLER."/page-1");
    }
} else {    
    header("Location: ".SITE_URL.CONTROLER."/page-1");
}