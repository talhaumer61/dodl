<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';

$condition = array ( 
                    'select'        =>  'blog_id, blog_name, blog_tags, blog_photo, blog_date, blog_description, blog_href',
                    'where'         => array( 
                                            'blog_href'    =>  cleanvars(ZONE)
                                            ,'is_deleted'   =>  0
                                            ,'blog_status'  =>  1
                                        ), 
                    'return_type'   =>  'single'
); 
$row = $dblms->getRows(BLOGS, $condition);

if($row){
  // CHECK FILE EXIST
  $blog_photo = SITE_URL_PORTAL.'uploads/images/default_blog.jpg';
  if(!empty($row['blog_photo'])){
    $blog_photo = SITE_URL_PORTAL.'uploads/images/blogs/'.$row['blog_photo'];
  }
  $share_description = get_dataHashingOnlyExp(html_entity_decode(html_entity_decode(substr($row['blog_description'], 0, 150))), true);
  
  $condition = array ( 
                      'select'        =>  'blog_name, blog_href, blog_photo, blog_date, blog_tags'
                      ,'where' 	      =>  array( 
                                              'is_deleted'   =>  0
                                              ,'blog_status'  =>  1
                                            )
                      ,'not_equal'    =>  array( 
                                              'blog_id'   =>  $row['blog_id']
                                            )
                      ,'order_by'     =>  ' RAND()'
                      ,'limit'        =>  ' 8'
                      ,'return_type'  =>  'all'
  ); 
  $BLOGS = $dblms->getRows(BLOGS, $condition);
  echo'
  <section class="course-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-12">
          <div class="blog">
            <div class="blog-image">
              <a href="javascript:void(0);">
                <img src="'.$blog_photo.'" alt="Blog Image" />
              </a>
            </div>
            <div class="blog-info clearfix">
              <div class="post-left">
                <ul class="mb-2">
                  <li>
                    <img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-22.svg" alt="Img" />
                    '.date('D d M, Y', strtotime($row['blog_date'])).'
                  </li>
                  <li>
                    <a class="" onclick="show_modal(\''.SITE_URL.'include/modals/share_options/share_options.php?title='.cleanvars($row['blog_name']).'&description='.$share_description.'&image='.cleanvars($blog_photo).'&url='.SITE_URL . 'blogs/' . $row['blog_href'].'\');"><i class="feather-share-2"></i> Share</a>
                  </li>
                </ul>
                <ul>
                  <li>
                    <img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-23.svg" alt="Img"/>
                    '.($row['blog_tags'] ? str_replace(",", ", ", $row['blog_tags']) : '').'
                  </li>
                </ul>
              </div>
            </div>
            <h3 class="blog-title">
              '.$row['blog_name'].'
            </h3>
            <div class="blog-content">
              '.html_entity_decode(html_entity_decode($row['blog_description'])).'
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-12">
          <div class="card post-widget blog-widget">
            <div class="card-header">
              <h4 class="card-title">Recent Blogs</h4>
            </div>
            <div class="card-body">
              <ul class="latest-posts">';
                foreach ($BLOGS as $key => $value) {                  
                  // CHECK FILE EXIST
                  $b_photo = SITE_URL_PORTAL.'uploads/images/default_blog.jpg';
                  if(!empty($row['blog_photo'])){
                    $b_photo = SITE_URL_PORTAL.'uploads/images/blogs/'.$value['blog_photo'];
                  }
                  echo'
                  <li>
                    <div class="post-thumb">
                      <a href="'.SITE_URL.'blogs/'.$value['blog_href'].'">
                        <img class="img-fluid" src="'.$b_photo.'" alt="Img" style="width: 70px; height: 70px; object-fit: cover;"/>
                      </a>
                    </div>
                    <div class="post-info">
                      <h4 class="line-clamp-2">
                        <a href="'.SITE_URL.'blogs/'.$value['blog_href'].'">'.$value['blog_name'].'</a>
                      </h4>
                      <p>
                        <img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-22.svg" alt="Img"/>
                        '.date('d M, Y', strtotime($value['blog_date'])).'
                      </p>
                    </div>
                  </li>';
                }
                echo'
              </ul>
            </div>
          </div>
          <div class="card tags-widget blog-widget tags-card">
            <div class="card-header">
              <h4 class="card-title">Latest Tags</h4>
            </div>
            <div class="card-body">
              <ul class="tags">';
                foreach ($BLOGS as $key => $value) {
                  foreach (explode(',',$value['blog_tags']) as $tag) {
                    echo'<li><a href="javascript:void(0);" class="tag">'.$tag.'</a></li>';
                  }
                }
                echo'
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>';
} else {    
  header("location: ".SITE_URL.CONTROLER."");
}
require_once 'footer.php';
?>