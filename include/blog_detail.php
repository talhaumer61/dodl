<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';

$condition = array ( 
                    'select'        =>  'blog_id, blog_name, blog_tags, blog_photo, blog_date, blog_description',
                    'where'         => array( 
                                            'blog_href'    =>  cleanvars(ZONE)
                                            ,'is_deleted'   =>  0
                                            ,'blog_status'  =>  1
                                        ), 
                    'return_type'   =>  'single'
); 
$row = $dblms->getRows(BLOGS, $condition);

if($row){
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
        <div class="col-lg-9 col-md-12">
          <div class="blog">
            <div class="blog-image">
              <a href="javascript:void(0);">
                <img src="'.SITE_URL_PORTAL.'uploads/images/blogs/'.$row['blog_photo'].'" alt="Blog Image" />
              </a>
            </div>
            <div class="blog-info clearfix">
              <div class="post-left">
                <ul class="mb-2">
                  <li>
                    <img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-22.svg" alt="Img" />
                    '.date('D d M, Y', strtotime($row['blog_date'])).'
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

        <div class="col-lg-3 col-md-12">
          <div class="card post-widget blog-widget">
            <div class="card-header">
              <h4 class="card-title">Recent Blogs</h4>
            </div>
            <div class="card-body">
              <ul class="latest-posts">';
                foreach ($BLOGS as $key => $value) {
                  echo'
                  <li>
                    <div class="post-thumb">
                      <a href="'.SITE_URL.'blogs/'.$value['blog_href'].'">
                        <img class="img-fluid" src="'.SITE_URL_PORTAL.'uploads/images/blogs/'.$value['blog_photo'].'" alt="Img" />
                      </a>
                    </div>
                    <div class="post-info">
                      <h4 class="line-clamp-2">
                        <a href="'.SITE_URL.'blogs/'.$value['blog_href'].'">'.$value['blog_name'].'</a>
                      </h4>
                      <p>
                        <img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-22.svg" alt="Img"/>
                        '.date('d M, Y', strtotime($row['blog_date'])).'
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