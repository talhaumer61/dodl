<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';
if(!empty($zone)){
  include("instructors_detail.php");
}else{
  include("instructors_list.php");
}
require_once 'footer.php';
?>