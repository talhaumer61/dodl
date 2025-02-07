<?php
$condition = array ( 
                       'select'       =>	'a.is_teacher, e.emply_request'
                      ,'join' 		    =>	'LEFT JOIN '.EMPLOYEES.' e ON e.emply_loginid = a.adm_id AND e.emply_status = 1 AND e.is_deleted = 0' 
                      ,'where'        =>	array( 
                                                   'a.adm_status'   => 1
                                                  ,'a.is_deleted'   => 0
                                                  ,'a.adm_id'       => cleanvars($_SESSION['userlogininfo']['LOGINIDA']) 
                                                )
                      ,'return_type'	=>	'single'
                    ); 
$ADMINS = $dblms->getRows(ADMINS.' a', $condition);


// NOTIFICATIONS
$today = date('Y-m-d');
$conditions = array ( 
                       'select'       =>	'n.not_id, n.not_title, n.not_description, n.start_date, n.end_date, n.dated'
                      ,'where'        =>	array( 
                                                   'n.not_status'   => 1
                                                  ,'n.id_type'      => 1
                                                  ,'n.is_deleted'   => 0
                                                )
                      ,'search_by'    =>  ' AND n.start_date <= "'.$today.'" AND n.end_date >= "'.$today.'"'
                      ,'order_by'     =>	' n.dated DESC'
                      ,'return_type'	=>	'count'
                    ); 
$countNot = $dblms->getRows(NOTIFICATIONS.' n', $conditions);

$conditions['return_type'] = 'all';
$NOTIFICATIONS = $dblms->getRows(NOTIFICATIONS.' n', $conditions);


echo'
<header class="header">
  <div class="header-fixed">
    <nav class="navbar navbar-expand-lg header-nav scroll-sticky">
      <div class="container">
        <div class="navbar-header">
          <a id="mobile_btn" href="javascript:void(0);">
            <span class="bar-icon">
              <span></span>
              <span></span>
              <span></span>
            </span>
          </a>
          <a href="'.SITE_URL.'" class="navbar-brand logo">
            <img src="'.SITE_URL.'assets/img/logo/logo.png" class="img-fluid" alt="Logo" />
          </a>
        </div>
        <div class="main-menu-wrapper">
          <div class="menu-header">
            <a href="'.SITE_URL.'" class="menu-logo">
              <img src="'.SITE_URL.'assets/img/logo/logo.png" class="img-fluid" alt="Logo"/>
            </a>
            <a id="menu_close" class="menu-close" href="javascript:void(0);">
              <i class="fas fa-times"></i>
            </a>
          </div>
          <ul class="main-nav">
            <li class="'.($varx == 'home' ? 'active' : '').'"><a class="'.($varx == 'home' ? 'fw-bold' : '').'" href="'.SITE_URL.'">Home</a></li>
            <li class="'.($varx == 'courses' ? 'active' : '').'"><a class="'.($varx == 'courses' ? 'fw-bold' : '').'" href="'.SITE_URL.'courses">Certificate Courses</a></li>
            <li class="'.($varx == 'trainings' ? 'active' : '').'"><a class="'.($varx == 'trainings' ? 'fw-bold' : '').'" href="'.SITE_URL.'trainings">Trainings</a></li>
            <li class="'.($varx == 'mastertrack' ? 'active' : '').'"><a class="'.($varx == 'mastertrack' ? 'fw-bold' : '').'" href="'.SITE_URL.'master-track-certificates">MasterTrack</a></li>
            <li class="'.($varx == 'degrees' ? 'active' : '').'"><a class="'.($varx == 'degrees' ? 'fw-bold' : '').'" href="'.SITE_URL.'degrees">Online Degree</a></li>';
            if(isset($user) && $user == 'student'){
              echo'<li class="login-link"><a href="'.SITE_URL.'/dashboard">Dashboard</a></li>';
            }else{
              echo'              
              <li class="login-link">
                <a href="'.SITE_URL.'signin">Sign In</a>
              </li>
              <li class="login-link">
                <a href="'.SITE_URL.'signup">Sign Up</a>
              </li>';
            }
            echo'
          </ul>
        </div>';
        if(isset($user) && $user == 'student'){
          echo'          
          <ul class="nav header-navbar-rht">';
            /*
            echo'
            <li class="nav-item cart-nav">
              <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <img src="'.SITE_URL.'assets/img/icon/cart.svg" alt="img" />
              </a>
              <div class="wishes-list dropdown-menu dropdown-menu-right">
                <div class="wish-header">
                  <a href="#">View Cart</a>
                  <a href="javascript:void(0)" class="float-end">Checkout</a>
                </div>
                <div class="wish-content">
                  <ul>';
                    for ($i=0; $i <3 ; $i++){
                      echo'
                      <li>
                        <div class="media">
                          <div class="d-flex media-wide">
                            <div class="avatar">
                              <a href="">
                                <img alt src="'.SITE_URL.'assets/img/course/course-04.jpg" />
                              </a>
                            </div>
                            <div class="media-body">
                              <h6>
                                <a href="">Learn Angular...</a>
                              </h6>
                              <p>By Dave Franco</p>
                              <h5>$200 <span>$99.00</span></h5>
                            </div>
                          </div>
                          <div class="remove-btn">
                            <a href="#" class="btn">Remove</a>
                          </div>
                        </div>
                      </li>';
                    }
                    echo'
                  </ul>
                  <div class="total-item">
                    <h6>Subtotal : $ 600</h6>
                    <h5>Total : $ 600</h5>
                  </div>
                </div>
              </div>
            </li>';*/

            // WISHLIST
            echo'
            <li class="nav-item wish-nav">
              <a href="javascript:void(0);" class="dropdown-toggle" id="wishlist_icon" data-bs-toggle="dropdown">
                <img src="'.SITE_URL.'assets/img/icon/wish.svg" alt="img" />
              </a>
              <div class="wishes-list dropdown-menu dropdown-menu-right">
                <div class="wish-content">
                  <ul id="wishlist_items">
                    <li class="text-center text-danger">Loading...</li>
                  </ul>
                </div>
              </div>
            </li>';
            
            // NOTIFICATION
            echo'
            <li class="nav-item noti-nav">
              <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <img src="'.SITE_URL.'assets/img/icon/notification.svg" alt="img" />
                <span class="badge bgg-yellow badge-pill" style="position: relative; top: -0.5rem; left: -1rem">'.$countNot.'</span>
              </a>
              <div class="notifications dropdown-menu dropdown-menu-right">';
                if($NOTIFICATIONS){
                  echo'
                  <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifications
                      <!--select>
                        <option>All</option>
                        <option>Unread</option>
                      </select-->
                    </span>
                    <a href="'.SITE_URL.'notifications" class="clear-noti">See All<i class="ms-1 fa-solid fa-paper-plane"></i></a>
                  </div>';
                }
                echo'
                <div class="noti-content">
                  <ul class="notification-list">';
                    if($NOTIFICATIONS){
                      foreach ($NOTIFICATIONS as $keyNot => $valNot) {  
                        $NotTitleFirst = substr($valNot['not_title'], 0, 1);                      
                        echo'
                        <li class="notification-message">
                          <div class="media d-flex">
                            <div>
                              <a href="'.SITE_URL.'notifications" class="avatar">
                                <!--img class="avatar-img" alt src="'.SITE_URL.'assets/img/user/user2.jpg" /-->
                                <div class="circle bg-warning text-white me-2" style="height: 30px !important;width: 30px !important;">'.$NotTitleFirst.'</div>
                              </a>
                            </div>
                            <div class="media-body">
                              <h6 class="mb-0">
                                <a href="'.SITE_URL.'notifications">'.$valNot['not_title'].'</a>
                              </h6>
                              <p>'.timeAgo($valNot['dated']).'</p>
                            </div>
                          </div>
                        </li>';
                      }
                    } else {
                      echo'<h6 class="text-center text-danger mb-0 p-3">No Notifications...!</h6>';
                    }
                    echo'
                  </ul>';
                  echo'
                </div>
              </div>
            </li>';

            // PROFILE OPTIONS
            echo'
            <li class="nav-item user-nav">
              <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <span class="user-img">
                  <img src="'.$_SESSION['userlogininfo']['LOGINPHOTO'].'" alt />
                  <span class="status online"></span>
                </span>
              </a>
              <div class="users dropdown-menu dropdown-menu-right" data-popper-placement="bottom-end" >
                <div class="user-header">
                  <div class="avatar avatar-sm">
                    <img src="'.$_SESSION['userlogininfo']['LOGINPHOTO'].'" alt="User Image" class="avatar-img rounded-circle" />
                  </div>
                  <div class="user-text">
                    <h6>'.$_SESSION['userlogininfo']['LOGINNAME'].'</h6>
                    <p class="text-muted mb-0">Learner</p>
                  </div>
                </div>
                <a class="dropdown-item" href="'.SITE_URL.'dashboard"><i class="feather-home me-1"></i> Dashboard</a>';
                if ($ADMINS['is_teacher'] != 1) {
                  echo'<a class="dropdown-item" onclick="switch_to_instructor();" style="cursor: pointer;"><i class="feather-refresh-cw me-1"></i> Instructor dashboard</a>';
                }else{
                  if($ADMINS['emply_request']	== 2){
                    echo'<a class="dropdown-item" style="cursor: pointer;" title="Request has sent and waiting for approval"><i class="feather-refresh-cw me-1"></i> Become Instructor <span class="badge badge-warning ms-1">Pending</span></a>';
                  }else{
                    echo'<a class="dropdown-item" href="'.SITE_URL.'signup-as-instructor" style="cursor: pointer;"><i class="feather-refresh-cw me-1"></i> Become Instructor</a>';
                  }
                }
                echo'
                <a class="dropdown-item" href="'.SITE_URL.'profile"><i class="feather-user me-1"></i> Profile</a>
                <a class="dropdown-item" href="'.SITE_URL.'profile-detail"><i class="feather-settings me-1"></i>Account Setting</a>
                <a class="dropdown-item" href="'.SITE_URL.'logout"><i class="feather-log-out me-1"></i> Logout</a>
              </div>
            </li>
          </ul>';
        }else{
          echo'
          <ul class="nav header-navbar-rht">
            <li class="nav-item">
              <a class="nav-link header-sign" href="'.SITE_URL.'signin">Sign In</a>
            </li>
            <li class="nav-item">
              <a class="nav-link header-sign" href="'.SITE_URL.'signup">Sign Up</a>
            </li>
          </ul>';
        }
        echo'        
      </div>
    </nav>
  </div>
</header>

<script>
  $(document).ready(function() {
      $("#wishlist_icon").click(function() { 
          $.ajax({
              type: "POST",
              url: "'.SITE_URL.'include/ajax/get_wishlist.php",
              data: { 
                method  : "__GET_WISHLIST" 
                ,url    : "'.CONTROLER.(ZONE ? '/'.ZONE : '').'"
              },
              success: function(response) {
                $("#wishlist_items").html(response);
              }
          });
      });
  });

  function switch_to_instructor(){
    $.ajax({
        type: "POST",
        url: "'.SITE_URL.'include/ajax/switch_to_instructor.php",
        success: function() {
          window.location.href = "'.SITE_URL_PORTAL.'login.php";
        }
    }); 
  }
</script>';
?>