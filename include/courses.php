<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';
if(!empty($zone) && $zone != 'categories'){
  include("course_detail.php");
}else{
  echo'
  <section class="course-content">
    <div class="container">
      <div class="row">';
        include("courses_list.php");
        include("filters_sidebar.php");
        echo'    
      </div>
    </div>
  </section>';
}
require_once 'footer.php';
?>