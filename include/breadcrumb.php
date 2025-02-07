<?php
$control_link = '';
$zone_link = '';

// NOT AUTH INDEXES 
$not_auth_index = array(
                       'instructors'
                      ,'verify-certificate'
                      ,'feedback'
                    );

if(!empty(CONTROLER)){
  if(CONTROLER == 'courses'){
    $control = 'certificate-courses';
  }
  $control_link = '<li class="breadcrumb-item active" aria-current="page">'.(!empty(ZONE) && !in_array(CONTROLER, $not_auth_index) ? '<a href="'.SITE_URL.$control.'">'.moduleName($control).'</a>' : moduleName($control)).'</li>';
}

if(!empty(ZONE) && !in_array(CONTROLER, $not_auth_index)){
  $zone_link = '<li class="breadcrumb-item" aria-current="page">'.moduleName(ZONE).'</li>';
}
echo'<br><br><br>
<hr>
<div class="breadcrumb-bar">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-12">
        <div class="breadcrumb-list">
          <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="'.SITE_URL.'">Home</a></li>
              '.$control_link.'
              '.$zone_link.'
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>';
?>