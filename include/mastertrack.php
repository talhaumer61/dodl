<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';

if(!empty($zone) && $control == 'master-track-detail'){
  include ('mastertrack/mastertrack_detail.php');
}else{
  require_once 'mastertrack/mastertrack_programs.php';
}
require_once 'footer.php';
?>