<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';

if(!empty(ZONE)){
  include("blog_detail.php");
}else{
  include("blog_list.php");
}
require_once 'footer.php';
?>