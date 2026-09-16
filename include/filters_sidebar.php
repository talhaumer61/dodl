<?php
// CATEGORIES
$condition = array ( 
                     'select' 		=>	'cct.cat_id, cct.cat_name,COUNT(c.id_cat) AS TotalCourses'
                    ,'join'       =>  'INNER JOIN '.ADMISSION_OFFERING.' c ON cct.cat_id=c.id_cat AND c.is_deleted = 0'
                    ,'where' 		  =>	array(
                                            'cct.cat_status ' 	 => '1'
                                            ,'cct.is_deleted' 	 => '0' 
                                            ,'c.admoff_status '	 => '1'
                                            ,'c.is_deleted' 	   => '0' 
                                            ,'cct.id_type' 	     => $_SESSION['id_type']
                                          )
                    ,'search_by'    =>  ' AND c.admoff_type IN (3,4)'
                    ,'group_by'     =>  'cct.cat_id'
                    ,'return_type'	=>	'all'
                  ); 
$curs_cat = $dblms->getRows(COURSES_CATEGORIES.' cct', $condition);
// TEACHERS
$condition = array ( 
                       'select'       =>	'e.emply_id,e.emply_name, COUNT(ca.id_curs) AS curs_count'
                      ,'join'         =>  'INNER JOIN '.EMPLOYEES.' AS e ON FIND_IN_SET(e.emply_id,ca.id_teacher)
                                           INNER JOIN '.COURSES.' AS c ON c.curs_id = ca.id_curs
                                           INNER JOIN '.COURSES_CATEGORIES.' AS cc ON FIND_IN_SET(cc.cat_id, c.id_cat) AND cc.id_type = '.$_SESSION['id_type'].''
                      ,'where'        =>	array(
                                                 'e.emply_status' => '1'
                                                ,'e.is_deleted'   => '0'  
                                              )
                      ,'group_by'     =>  'e.emply_id'
                      ,'return_type'  =>	'all'
                    ); 
$instructor = $dblms->getRows(ALLOCATE_TEACHERS.' ca', $condition, $sql);

echo'
<div class="col-lg-3">
  <div class="filter-clear">
    <div class="clear-filter d-flex align-items-center">
      <h4><i class="feather-filter"></i>Filters</h4>
      <div class="clear-text">
        <p><a href="'.SITE_URL.CONTROLER.'">RESET</a></p>
      </div>
    </div>';
    if(CONTROLER == 'trainings'){
      echo'
      <div class="card search-filter categories-filter-blk">
        <div class="card-body">
          <div class="filter-widget mb-0">
            <div class="categories-head d-flex align-items-center">
              <h4>Training Types</h4>
              <i class="fas fa-angle-down"></i>
            </div>';
            foreach (get_LeanerType() as $key => $val) {
              echo'
              <div>
                <label class="custom_check">
                  <input type="checkbox" data-search-cb="a.id_type" onchange="call_ajax()" name="select_specialist" value="'.$key.'"/>
                  <span class="checkmark"></span>'.$val.'
                </label>
              </div>';
            }
            echo'
          </div>
        </div>
      </div>';
    } else {
      echo'
      <div class="card search-filter categories-filter-blk">
        <div class="card-body">
          <div class="filter-widget mb-0">
            <div class="categories-head d-flex align-items-center">
              <h4>Course Types</h4>
              <i class="fas fa-angle-down"></i>
            </div>';
            foreach (get_curs_status() as $key => $val) {
              echo'
              <div>
                <label class="custom_check">
                  <input type="checkbox" data-search-cb="c.curs_type_status" onchange="call_ajax()" name="select_specialist" value="'.$key.'"/>
                  <span class="checkmark"></span>'.$val.'
                </label>
              </div>';
            }
            echo'
          </div>
        </div>
      </div>';
    }
    echo'
    <div class="card search-filter categories-filter-blk">
      <div class="card-body">
        <div class="filter-widget mb-0">
          <div class="categories-head d-flex align-items-center">
            <h4>'.(CONTROLER == 'trainings' ? 'Training' : 'Course').' Categories</h4>
            <i class="fas fa-angle-down"></i>
          </div>
          <div style="max-height: 450px; overflow-y: scroll;">';
            foreach ($curs_cat as $key => $cat) {
              echo'
              <div>
                <label class="custom_check">
                  <input type="checkbox" data-search-cb="c.id_cat" onchange="call_ajax()" name="select_specialist" value="'.$cat['cat_id'].'" '.($view == $cat['cat_id'] ? 'checked' : '').'/>
                  <span class="checkmark"></span>'.$cat['cat_name'].' ('.$cat['TotalCourses'].')
                </label>
              </div>';
            }
            echo'
          </div>
        </div>
      </div>
    </div>
    <div class="card search-filter">
      <div class="card-body">
        <div class="filter-widget mb-0">
          <div class="categories-head d-flex align-items-center">
            <h4>Instructors</h4>
            <i class="fas fa-angle-down"></i>
          </div>
          <div style="max-height: 450px; overflow-y: scroll;">';
            foreach ($instructor as $key => $teacher) {
              echo'
              <div>
                <label class="custom_check">
                  <input type="checkbox" name="select_specialist" data-search-cb="ca.id_teacher" onchange="call_ajax()" name="select_specialist2" value="'.$teacher['emply_id'].'"/>
                  <span class="checkmark"></span>'.$teacher['emply_name'].' ('.$teacher['curs_count'].')
                </label>
              </div>';
            }
            echo'
          </div>
        </div>
      </div>
    </div>';
    /*
    echo'
    <div class="card search-filter">
      <div class="card-body">
        <div class="filter-widget mb-0">
          <div class="categories-head d-flex align-items-center">
            <h4>Price</h4>
            <i class="fas fa-angle-down"></i>
          </div>
          <div>
            <label class="custom_check custom_one">
              <input type="radio" name="select_specialist" />
              <span class="checkmark"></span> All (18)
            </label>
          </div>
          <div>
            <label class="custom_check custom_one">
              <input type="radio" name="select_specialist" />
              <span class="checkmark"></span> Free (3)
            </label>
          </div>
          <div>
            <label class="custom_check custom_one mb-0">
              <input
                type="radio"
                name="select_specialist"
                checked
              />
              <span class="checkmark"></span> Paid (15)
            </label>
          </div>
        </div>
      </div>
    </div>';*/
    if(CONTROLER != 'trainings'){
      echo'
      <div class="card post-widget">
        <div class="card-body">
          <div class="latest-head">
            <h4 class="card-title">Latest Courses</h4>
          </div>
          <ul class="latest-posts">';
            $condition = array ( 
                                  'select' 		  =>	'c.curs_id, c.curs_name, c.curs_wise, c.curs_type_status, c.duration, c.curs_href, c.curs_icon, c.curs_photo, COUNT(l.lesson_id) TotalLesson'
                                  ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = c.curs_id AND a.admoff_type = 3
                                                      LEFT JOIN '.COURSES_LESSONS.' l ON l.id_curs = c.curs_id AND l.is_deleted = 0 AND l.lesson_status = 1'
                                  ,'where' 		    =>	array( 
                                                          'c.curs_status' 	    => '1' 
                                                          ,'c.is_deleted' 	    => '0' 
                                                          )
                                  ,'group_by'     =>  'c.curs_id'
                                  ,'order_by'     =>  'RAND()'
                                  ,'limit'        =>  '6'
                                  ,'return_type'	=>	'all'
                              );
            $row = $dblms->getRows(COURSES.' c', $condition); 
            foreach ($row as $key => $value) {
              // CHECK FILE EXIST
              $photo    = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
              $file_url = SITE_URL_PORTAL.'uploads/images/courses/'.$value['curs_photo'];
              if (check_file_exists($file_url)) {
                $photo = $file_url;
              }

              echo'
              <li>
                <div class="post-thumb">
                  <a '.($value['curs_type_status'] == '1' ? '' : 'href="'.SITE_URL.'courses/'.$value['curs_href'].'"').'>
                    <img class="img-fluid" src="'.$photo.'" alt/>
                  </a>
                </div>
                <div class="post-info free-color">
                  <h4>
                    <a '.($value['curs_type_status'] == '1' ? '' : 'href="'.SITE_URL.'courses/'.$value['curs_href'].'"').'>'.$value['curs_name'].'</a>
                  </h4>
                  <p>'.$value['duration'].' '.get_CourseWise($value['curs_wise']).($value['duration'] > 1 ? 's' : '').'</p>
                </div>
              </li>';
            }
            echo'
          </ul>
        </div>
      </div>';
    }
    echo'
  </div>
</div>';
?>
<script>
function clearFormElements() {
  // Get all input, checkbox, and select elements
  const formElements = document.querySelectorAll('input, select, textarea');

  // Clear the value of each element
  formElements.forEach(element => {
    console.log(element.type);
    if (element.type === 'checkbox' || element.type === 'radio') {
      element.checked = false;
    } else {
      element.value = '';
    }
  }); 
  call_ajax();
}
</script>