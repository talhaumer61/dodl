<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';
if(!empty($zone) && $control == 'degrees'){
    require_once 'degrees/degree_programs.php';
}elseif(!empty($zone) && $control == 'degree-detail'){
    include ('degrees/degree_detail.php');
}else{
    require_once 'degrees/degree_finder.php';
}
require_once 'footer.php';
?>