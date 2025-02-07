<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';
echo '
<div class="page-banner">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-12">
        <h1 class="mb-0">' . moduleName(CONTROLER) . '</h1>
      </div>
    </div>
  </div>
</div>
<div class="page-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="terms-content">
          <div class="terms-text">
            <h3 class="text-center">These terms should provide clarity and guidance for users engaging with the online distance learning platform while also protecting the website\'s interests and maintaining a positive learning environment.</h3>
            <hr>';
            foreach ($terms as $key => $value) {
              echo'
              <h5>'.$value['title'].'</h5>
              <p>'.$value['content'].'</p>';
            }
            echo'            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>';
require_once 'footer.php';
?>