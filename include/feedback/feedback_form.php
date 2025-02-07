<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';
require_once 'include/breadcrumb.php';
checkCpanelLMSSTDLogin();

include_once 'query.php';

if(!empty(ZONE)){
  $conditions = array ( 
                        'select'        =>	'f.feedback_id'
                        ,'where'        =>	array( 
                                                'f.id_std'         => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                ,'f.id_enroll'      => cleanvars(ZONE)
                                              )
                        ,'return_type'	=>	'count'
                      );
  $countFeedback = $dblms->getRows(STUDENT_FEEDBACK.' f', $conditions);
  if(!$countFeedback){
    $condition = array (
                           'select' 		  =>	'ec.secs_id, ec.id_curs, ec.id_mas, ec.id_ad_prg, c.curs_name, mt.mas_name, ap.program'
                          ,'join'         =>	'LEFT JOIN '.COURSES.' c ON c.curs_id = ec.id_curs
                                               LEFT JOIN '.MASTER_TRACK.' mt ON mt.mas_id = ec.id_mas
                                               LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ec.id_ad_prg' 
                          ,'where' 		    =>	array( 
                                                     'ec.id_std'        => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                    ,'ec.secs_id'       => ZONE
                                                    ,'ec.secs_status'   => 1
                                                    ,'ec.is_deleted'    => 0
                                                  )
                          ,'return_type'	=>	'single'
                        ); 
    $row = $dblms->getRows(ENROLLED_COURSES.' ec', $condition);
    if($row['program']) {
      $cert_type  = '1';
      $cert_name  = $row['program'];
    } elseif ($row['mas_name']) {
      $cert_type  = '2';
      $cert_name  = $row['mas_name'];
    } elseif ($row['curs_name']) {
      $cert_type  = '3';
      $cert_name  = $row['curs_name'];
    }

    // FEEDBACK QUESTIONS
    $conditions = array ( 
                           'select'        =>	'id, type, question, options'
                          ,'where'        =>	array( 
                                                   'status'       => 1
                                                  ,'is_deleted'   => 0
                                                )
                          ,'return_type'	=>	'all'
                        );
    $FEEDBACK_QUESTIONS = $dblms->getRows(FEEDBACK_QUESTIONS, $conditions);
    $sr = 0;
    echo'
    <section class="course-content">
      <div class="container">
        <div class="row">
          <div class="col-xl-12 col-lg-12 mb-2 col-md-12">
            <form action="" method="post">
              <div class="settings-widget">
                <div class="settings-inner-blk p-0">
                  <div class="sell-course-head comman-space text-center">
                    <h3>Course Completion Feedback Form</h3>
                    <p>Thank you for completing the course! Your feedback is valuable to us and will help improve the quality of our courses. Please take a few moments to share your experience.</p>
                  </div>
                  <div class="comman-space pb-0">
                    <div class="settings-tickets-blk course-instruct-blk table-responsive">
                      <table class="table table-nowrap mb-3">
                        <thead>
                          <tr>
                            <th colspan="4" class="text-center">Section 1: General Information</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <th width="15%">Name:</th>
                            <td width="35%">'.$_SESSION['userlogininfo']['LOGINNAME'].'</td>
                            <th width="15%">Email:</th>
                            <td width="35%">'.$_SESSION['userlogininfo']['LOGINEMAIL'].'</td>
                          </tr>
                          <tr>
                            <th width="15%">Course:</th>
                            <td colspan="3">
                              '.$cert_name.'
                              <input type="hidden" id="id_enroll" name="id_enroll" required value="'.ZONE.'">
                              <input type="hidden" id="cert_type" name="cert_type" required value="'.$cert_type.'">
                              <input type="hidden" id="cert_name" name="cert_name" required value="'.$cert_name.'">
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      
                      <table class="table table-nowrap mb-3">
                        <thead>
                          <tr>
                            <th colspan="5" class="text-center">Section 2: Course Feedback</th>
                          </tr>
                        </thead>
                        <tbody>';
                          foreach ($FEEDBACK_QUESTIONS as $keyRow => $valRow) {
                            if(in_array($valRow['id'], array(1,2,3,4))){
                              $sr++;
                              echo'
                              <tr>
                                <th colspan="5">
                                  '.$sr.'. '.$valRow['question'].'
                                  <input type="hidden" id="question['.$sr.']" name="question['.$sr.']" required value="'.$valRow['id'].'">
                                </th>
                              </tr>
                              <tr>';
                                if($valRow['type'] == 3){
                                  $options = json_decode(html_entity_decode($valRow['options']), true);
                                  foreach ($options as $keyOpt => $valOpt) {
                                      echo'
                                      <td width="20%">';
                                        if(!empty($valOpt)){
                                          echo'
                                          <div class="form-switch">
                                            <input class="form-check-input" id="answer['.$sr.']" name="answer['.$sr.']" required value="'.$keyOpt.'" type="radio">
                                            <label class="form-check-label mb-0">'.$valOpt.'</label>
                                          </div>';
                                          echo'
                                      </td>';
                                    }
                                  }
                                } else {
                                  echo'
                                  <td colspan="5">
                                      <textarea class="form-control" name="answer['.$sr.']"></textarea>
                                  </td>';
                                }
                                echo'
                              </tr>';
                            }
                          }
                          echo'
                        </tbody>
                      </table>
                      
                      <table class="table table-nowrap mb-3">
                        <thead>
                          <tr>
                            <th colspan="5" class="text-center">Section 3: Instructor Feedback</th>
                          </tr>
                        </thead>
                        <tbody>';
                          foreach ($FEEDBACK_QUESTIONS as $keyRow => $valRow) {
                            if(in_array($valRow['id'], array(5,6))){
                              $sr++;
                              echo'
                              <tr>
                                <th colspan="5">
                                  '.$sr.'. '.$valRow['question'].'
                                  <input type="hidden" id="question['.$sr.']" name="question['.$sr.']" required value="'.$valRow['id'].'">
                                </th>
                              </tr>
                              <tr>';
                                if($valRow['type'] == 3){
                                  $options = json_decode(html_entity_decode($valRow['options']), true);
                                  foreach ($options as $keyOpt => $valOpt) {
                                      echo'
                                      <td width="20%">';
                                        if(!empty($valOpt)){
                                          echo'
                                          <div class="form-switch">
                                            <input class="form-check-input" id="answer['.$sr.']" name="answer['.$sr.']" required value="'.$keyOpt.'" type="radio">
                                            <label class="form-check-label mb-0">'.$valOpt.'</label>
                                          </div>';
                                          echo'
                                      </td>';
                                    }
                                  }
                                } else {
                                  echo'
                                  <td colspan="5">
                                      <textarea class="form-control" name="answer['.$sr.']"></textarea>
                                  </td>';
                                }
                                echo'
                              </tr>';
                            }
                          }
                          echo'
                        </tbody>
                      </table>
                      
                      <table class="table table-nowrap mb-3">
                        <thead>
                          <tr>
                            <th colspan="5" class="text-center">Section 4: Course Experience</th>
                          </tr>
                        </thead>
                        <tbody>';
                          foreach ($FEEDBACK_QUESTIONS as $keyRow => $valRow) {
                            if(in_array($valRow['id'], array(7,8))){
                              $sr++;
                              echo'
                              <tr>
                                <th colspan="5">
                                  '.$sr.'. '.$valRow['question'].'
                                  <input type="hidden" id="question['.$sr.']" name="question['.$sr.']" required value="'.$valRow['id'].'">
                                </th>
                              </tr>
                              <tr>';
                                if($valRow['type'] == 3){
                                  $options = json_decode(html_entity_decode($valRow['options']), true);
                                  foreach ($options as $keyOpt => $valOpt) {
                                      echo'
                                      <td width="20%">';
                                        if(!empty($valOpt)){
                                          echo'
                                          <div class="form-switch">
                                            <input class="form-check-input" id="answer['.$sr.']" name="answer['.$sr.']" required value="'.$keyOpt.'" type="radio">
                                            <label class="form-check-label mb-0">'.$valOpt.'</label>
                                          </div>';
                                          echo'
                                      </td>';
                                    }
                                  }
                                } else {
                                  echo'
                                  <td colspan="5">
                                      <textarea class="form-control" name="answer['.$sr.']"></textarea>
                                  </td>';
                                }
                                echo'
                              </tr>';
                            }
                          }
                          echo'
                        </tbody>
                      </table>
                      
                      <table class="table table-nowrap mb-3">
                        <thead>
                          <tr>
                            <th colspan="5" class="text-center">Section 5: Assessment</th>
                          </tr>
                        </thead>
                        <tbody>';
                          foreach ($FEEDBACK_QUESTIONS as $keyRow => $valRow) {
                            if(in_array($valRow['id'], array(9))){
                              $sr++;
                              echo'
                              <tr>
                                <th colspan="5">
                                  '.$sr.'. '.$valRow['question'].'
                                  <input type="hidden" id="question['.$sr.']" name="question['.$sr.']" required value="'.$valRow['id'].'">
                                </th>
                              </tr>
                              <tr>';
                                if($valRow['type'] == 3){
                                  $options = json_decode(html_entity_decode($valRow['options']), true);
                                  foreach ($options as $keyOpt => $valOpt) {
                                      echo'
                                      <td width="20%">';
                                        if(!empty($valOpt)){
                                          echo'
                                          <div class="form-switch">
                                            <input class="form-check-input" id="answer['.$sr.']" name="answer['.$sr.']" required value="'.$keyOpt.'" type="radio">
                                            <label class="form-check-label mb-0">'.$valOpt.'</label>
                                          </div>';
                                          echo'
                                      </td>';
                                    }
                                  }
                                } else {
                                  echo'
                                  <td colspan="5">
                                      <textarea class="form-control" name="answer['.$sr.']"></textarea>
                                  </td>';
                                }
                                echo'
                              </tr>';
                            }
                          }
                          echo'
                        </tbody>
                      </table>
                      
                      <table class="table table-nowrap mb-3">
                        <thead>
                          <tr>
                            <th colspan="5" class="text-center">Section 6: Overall Satisfaction</th>
                          </tr>
                        </thead>
                        <tbody>';
                          foreach ($FEEDBACK_QUESTIONS as $keyRow => $valRow) {
                            if(in_array($valRow['id'], array(10,11,12,13,14,15,16))){
                              $sr++;
                              echo'
                              <tr>
                                <th colspan="5">
                                  '.$sr.'. '.$valRow['question'].'
                                  <input type="hidden" id="question['.$sr.']" name="question['.$sr.']" required value="'.$valRow['id'].'">
                                </th>
                              </tr>
                              <tr>';
                                if($valRow['type'] == 3){
                                  $options = json_decode(html_entity_decode($valRow['options']), true);
                                  foreach ($options as $keyOpt => $valOpt) {
                                      echo'
                                      <td width="20%">';
                                        if(!empty($valOpt)){
                                          echo'
                                          <div class="form-switch">
                                            <input class="form-check-input" id="answer['.$sr.']" name="answer['.$sr.']" required value="'.$keyOpt.'" type="radio">
                                            <label class="form-check-label mb-0">'.$valOpt.'</label>
                                          </div>';
                                          echo'
                                      </td>';
                                    }
                                  }
                                } else {
                                  echo'
                                  <td colspan="5">
                                      <textarea class="form-control" name="answer['.$sr.']"></textarea>
                                  </td>';
                                }
                                echo'
                              </tr>';
                            }
                          }
                          echo'
                        </tbody>
                      </table>                        
                    </div>
                  </div>
                  <div class="sell-course-head comman-space">
                    <p>Please submit your feedback by clicking the submit button below. Thank you for your time and valuable insights!</p>
                    <p>Thank you again for your participation!</p>
                  </div>
                </div>
              </div>
              <div class="container-fluid">
                <div class="update-profile row">
                  <div class="col">
                    <a href="'.SITE_URL.'student/courses" class="btn btn-wish w-100 mr-1"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                  </div>                      
                  <div class="col">
                    <button name="submit_feedback" type="submit" class="btn btn-enroll w-100">Submit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>';
  } else {
    echo'<script>window.location.href = "'.SITE_URL.'certificate-print/'.ZONE.'";</script>';
  }
} else {  
  $varx = 'home';
  echo'<script>window.location.href = "'.SITE_URL.'dashboard";</script>';
}
require_once 'include/footer.php';
?>