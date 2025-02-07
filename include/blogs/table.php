<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();
session_start();

$search_field = array("blog_name");
$searches     = (isset($_POST['word'])) ? json_decode($_POST['word'], true) : "";
$search_by    = ""; 

if ($search_field) {
  foreach ($searches as $key => $search) {
    $search_by .= " AND ( " .implode(" LIKE '%$search%' OR ",$search_field)." LIKE '%$search%' )";
  }
}


$condition = array ( 
                    'select'        =>  'blog_name, blog_href, blog_photo, blog_date, blog_tags, blog_description'
                    ,'where' 	      =>  array( 
                                             'is_deleted'   =>  0
                                            ,'blog_status'  =>  1
                                          )
                    ,'search_by'    =>  ''.$search_by.''
                    ,'return_type'  =>  'all'
); 
$table = BLOGS;
include '../pagination.php';
echo'
<div class="row">';
  foreach ($values as $key => $row) {
    // CHECK FILE EXIST         
    $blog_photo = SITE_URL_PORTAL.'uploads/images/default_blog.jpg';
    if(!empty($row['blog_photo'])){
      $file_url = SITE_URL_PORTAL.'uploads/images/blogs/'.$row['blog_photo'];
      if (check_file_exists($file_url)) {
        $blog_photo = $file_url;
      }
    }
    echo'
    <div class="col-lg-6 col-md-12">
      <div class="blog border">
        <div class="blog-image mb-0">
          <a href="'.SITE_URL.'blogs/'.$row['blog_href'].'">
            <img class="img-fluid" src="'.$blog_photo.'" alt="Post Image">
          </a>
        </div>
        <div class="p-3">
          <div class="blog-info clearfix">
            <div class="post-left">
              <ul>
                <li><img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-22.svg" alt="Img">'.date('D d M, Y', strtotime($row['blog_date'])).'</li>
              </ul>
            </div>
          </div>
          <h3 class="blog-title"><a href="'.SITE_URL.'blogs/'.$row['blog_href'].'">'.$row['blog_name'].'</a></h3>
          <div class="blog-content blog-read">
            <div class="line-clamp-3 mb-3">'.html_entity_decode(html_entity_decode($row['blog_description'])).'</div>
            <a href="'.SITE_URL.'blogs/'.$row['blog_href'].'" class="read-more btn btn-primary">Read More</a>
          </div>
        </div>
      </div>
    </div>';
  }
  echo'
</div>
<div class="row">
  <div class="col-md-12">
  '.main_pg($count,$limit,$page,$next,$prev,$srno).'
  </div>
</div>';
?>