<?php
require_once ("float_buttons.php");
// require_once ("tawkto.php");
      echo '
      <script src="https://elfsightcdn.com/platform.js" async></script>
      <div class="elfsight-app-bea944d3-90cc-4a94-8528-61de3ae36bfa" data-elfsight-app-lazy></div>';
      if(CONTROLER != 'feedback'){
        echo'
        <footer class="footer">
          <div class="footer-top aos" data-aos="fade-up">
            <div class="container">
              <div class="row">
                <div class="col-lg-4 col-md-6">
                  <div class="footer-widget footer-about">
                    <div class="footer-logo">
                      <img src="'.SITE_URL.'assets/img/logo/logo.png" alt="logo" />
                    </div>
                    <div class="footer-about-content">
                      <p>
                      MUL was founded in 1986 by Shaykh-ul-Islam Prof. Dr M. Tahir-ul-Qadri, patron-in-chief of Minhaj ul Quran International. Degree awarding status was granted by Govt. of the Punjab. The HEC of Pakistan has also recognized MUL, as ‘W3‘ ranking University.
                      </p>
                    </div>
                    <div class="social-icons">';
                      // Loop through social media links and display them as icons
                      foreach (getSocialMediaLinks() as $key => $value) {
                          echo '
                          <a href="'.$value['url'].'" target="_blank" class="me-2" style="font-size: 26px;" title="'.$value['name'].'">
                              <i class="'.$value['icon'].' fs-24" style="color: '.$value['color'].';"></i>
                          </a>';
                      }
                    echo '
                    </div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-6">
                  <div class="footer-widget footer-menu">
                    <h2 class="footer-title">Quick Access</h2>
                      <ul>
                        <li><a href="'.SITE_URL.'"><i class="fas fa-home"></i>Home</a></li>
                        <li><a href="'.SITE_URL.'instructors"><i class="fas fa-user-tie"></i>Instructors</a></li>
                        <li><a href="'.SITE_URL.'courses"><i class="fas fa-book-reader"></i>Certificate Courses</a></li>
                        <li><a href="'.SITE_URL.'trainings"><i class="fas fa-book-reader"></i>Trainings</a></li>
                        <li><a href="'.SITE_URL.'master-track-certificates"><i class="fas fa-book"></i>MasterTrack</a></li>
                        <li><a href="'.SITE_URL.'degrees"><i class="fas fa-user-graduate"></i>Online Degree</a></li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-2 col-md-6">
                  <div class="footer-widget footer-menu">
                    <h2 class="footer-title">For Student</h2>
                    <ul>';                  
                      if(isset($_SESSION['userlogininfo'])){
                        echo'
                        <li><a href="'.SITE_URL.'dashboard"><i class="fas fa-layer-group"></i>Dashboard</a></li>
                        <li><a href="'.SITE_URL.'profile"><i class="fas fa-user-alt"></i>Profile</a></li>
                        <li><a href="'.SITE_URL.'profile-detail"><i class="fas fa-user-cog"></i>Account Setting</a></li>
                        <li><a href="'.SITE_URL.'student/courses"><i class="fas fa-book-reader"></i>My Courses</a></li>';
                        if ($ADMINS['is_teacher'] != 1) {
                          echo'<li><a onclick="switch_to_instructor();" style="cursor: pointer;"><i class="fas fa-layer-group"></i>Instructor dashboard</a></li>';
                        }else{
                          if($ADMINS['emply_request']	== 2){
                            // echo'<li><a style="cursor: pointer;" title="Request has sent and waiting for approval">Become Instructor <span class="badge badge-warning ms-1">Pending</span></a></li>';
                          }else{
                            echo'<li><a href="'.SITE_URL.'signup-as-instructor" style="cursor: pointer;"><i class="fas fa-user-tie"></i>Become Instructor</a></li>';
                          }
                        }
                      } else {
                        echo'
                        <li><a href="'.SITE_URL.'signin"><i class="fas fa-sign-in-alt"></i>Sign in</a></li>
                        <li><a href="'.SITE_URL.'signup"><i class="fas fa-user-plus"></i>Sign up</a></li>';
                      }
                      echo'
                      <li><a href="'.SITE_URL.'blogs"><i class="fas fa-blog"></i>Blogs</a></li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6">
                  <div class="footer-widget footer-contact">
                    <h2 class="footer-title">News letter</h2>
                    <div class="news-letter">
                      <form>
                        <input type="text" class="form-control" placeholder="Enter your email address" name="email"/>
                      </form>
                    </div>
                    <div class="footer-contact-info">
                      <div class="footer-address">
                        <img src="'.SITE_URL.'assets/img/icon/icon-20.svg" alt class="img-fluid" />
                        <p>'.SITE_ADDRESS.'</p>
                      </div>
                      <p>
                        <img src="'.SITE_URL.'assets/img/icon/icon-19.svg" alt class="img-fluid"/>
                        <a href="mailto:'.SMTP_EMAIL.'">'.SMTP_EMAIL.'</a>
                      </p>
                      <p class="mb-0">
                        <img src="'.SITE_URL.'assets/img/icon/icon-21.svg" alt class="img-fluid"/>
                        '.SITE_PHONE.'
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="footer-bottom">
            <div class="container">
              <div class="copyright">
                <div class="row">
                  <div class="col-md-3">
                    <div class="privacy-policy">
                      <ul>
                        <li><a href="'.SITE_URL.'terms">Terms</a></li>
                        <li><a href="'.SITE_URL.'privacy">Privacy</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="copyright-text">
                      <p class="mb-0">
                        '.SITE_NAME.' '.COPY_RIGHTS_ORG.' <a href="'.COPY_RIGHTS_URL.'" class="text-primary" target="_blank">'.COPY_RIGHTS.'</a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </footer>';
      }
      echo'
    </div>
    <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="Modal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div id="modal_detail"></div>
        </div>
      </div>
    </div>

    <!-- JS here -->
    <script src="'.SITE_URL.'assets/js/moment.min.js"></script>
    <script src="'.SITE_URL.'assets/js/moment-timezone-with-data.min.js"></script>
    <script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>    
    <script src="'.SITE_URL.'assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
    <script src="'.SITE_URL.'assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>
    <script src="'.SITE_URL.'assets/js/bootstrap.bundle.min.js"></script>
    <script src="'.SITE_URL.'assets/js/validation.js"></script>
    <script src="'.SITE_URL.'assets/js/jquery.waypoints.js"></script>
    <script src="'.SITE_URL.'assets/js/jquery.counterup.min.js"></script>
    <script src="'.SITE_URL.'assets/plugins/select2/js/select2.min.js"></script>
    <script src="'.SITE_URL.'assets/js/owl.carousel.min.js"></script>
    <script src="'.SITE_URL.'assets/plugins/slick/slick.js"></script>
    <script src="'.SITE_URL.'assets/plugins/aos/aos.js"></script>
    <script src="'.SITE_URL.'assets/js/plugin.js"></script>
    <script src="'.SITE_URL.'assets/js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script>
      function confirm_modal( delete_url ) {
        swal( {
            title: "Are you sure?",
            text: "Are you sure that you want to delete this information?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            cancelButtonColor: "#f5f7fa",
            confirmButtonColor: "#ed5e5e"
        }, function () {
            $.ajax( {
                url: delete_url,
                type: "POST"
            } )
            .done( function ( data ) {
                swal( {
                    title: "Deleted",
                    text: "Information has been successfully deleted",
                    type: "success"
                }, function () {
                    location.reload();
                } );
            } )
            .error( function ( data ) {
                swal( "Oops", "We couldn\'t\ connect to the server!", "error" );
            } );
        } );
      }
      function show_modal(modal_url) {
          $.ajax({
              url : modal_url,
              success : function(response){
                  $("#Modal").html(response)
                  $("#Modal").modal("show");
              }
          })
      }
      function add_to_wishlist(id, wish_type, detail = ""){
        $.ajax({
          url : "'.SITE_URL.'include/wishlist/query.php",
          type : "post",
          dataType: "json",
          data : {
                   "wishlist"         : id
                  ,"wishlist_type"    : wish_type
                },
          success : function(response){
            if(response.msg == "add"){
              console.log(response.id);
              if(detail == 3 || detail == 4){
                $("[data-id="+id+"]").html(`<a class="btn btn-wish w-100" href="javascript:;" onclick="remove_from_wishlist(${response.id},${id},${wish_type},${detail})"><i class="fa-solid fa-heart"></i> Remove Wishlist</a>`);
              } else if(detail == 2){
                $("[data-id="+id+"]").html(`<a class="btn btn-wish mx-1" style="float: right;" href="javascript:;" onclick="remove_from_wishlist(${response.id},${id},${wish_type},2)"><i class="fa-solid fa-heart"></i> Remove Wishlist</a>`);
              }else{
                $("[data-id="+id+"]").html(`<a href="javascript:;" onclick="remove_from_wishlist(${response.id},${id},${wish_type})"><i class="fa-solid fa-heart"></i></a>`);
              }
              ttext = "Success! Added to Wishlist";
              color = "success";
            } else if(response.msg == "already"){
              ttext = "Warning! Already in Wishlist or Enrolled";
              color = "warning";
            } else if(response.msg == "login"){
              ttext = "Warning! You are not logged in";
              color = "danger";
            } else if(response.msg == "error"){
              ttext = "Error! Something went wrong.";
              color = "danger";
            }
            Toastify({
              newWindow: !0,
              text: ttext,
              gravity: "top",
              position: "right",
              className: "bg-"+color,
              stopOnFocus: !0,
              offset: "50",
              duration: "2000",
              close: true,
              style: "style",
            }).showToast();
          }
        })
      }      
      function remove_from_wishlist(wl_id, id, wish_type, detail=""){
        $.ajax({
          url : "'.SITE_URL.'include/wishlist/query.php",
          type : "post",
          dataType: "json",
          data : {"remove_wishlist" : wl_id},
          success : function(response){
            if(response.msg == "delete"){
              if(detail == 3 || detail == 4){
                $("[data-id="+id+"]").html(`<a class="btn btn-wish w-100" href="javascript:;" onclick="add_to_wishlist(${id},${wish_type},${detail})"><i class="fa-regular fa-heart"></i> Add to Wishlist</a>`);
              } else if(detail == 2){
                $("[data-id="+id+"]").html(`<a class="btn btn-wish mx-1" style="float: right;" href="javascript:;" onclick="add_to_wishlist(${id},${wish_type},2)"><i class="fa-regular fa-heart"></i> Add to Wishlist</a>`);
              }else{
                $("[data-id="+id+"]").html(`<a href="javascript:;" onclick="add_to_wishlist(${id},${wish_type})"><i class="fa-regular fa-heart"></i></a>`);
              }
              ttext = "Successfully! Remove from Wishlist";
            } else {
              ttext = "Warning! Error in Wishlist";
            }
            Toastify({
              newWindow: !0,
              text: ttext,
              gravity: "top",
              position: "right",
              className: "bg-info",
              stopOnFocus: !0,
              offset: "50",
              duration: "2000",
              close: true,
              style: "style",
            }).showToast();
          }
        })
      }
      function applyClampText(context = document) {
        context.querySelectorAll(".clamp-text").forEach(function(el) {
          // Skip if already processed
          if (el.nextElementSibling && el.nextElementSibling.classList.contains("show-more-btn")) return;

          const lineLimit = el.dataset.lines ? parseInt(el.dataset.lines) : 5;
          el.style.webkitLineClamp = lineLimit;

          // Clone to measure overflow
          const clone = el.cloneNode(true);
          clone.style.position = "absolute";
          clone.style.visibility = "hidden";
          clone.style.maxHeight = "none";
          clone.style.webkitLineClamp = "unset";
          document.body.appendChild(clone);

          const actualHeight = clone.scrollHeight;
          const lineHeight = parseInt(window.getComputedStyle(el).lineHeight);
          const maxHeight = lineHeight * lineLimit;
          document.body.removeChild(clone);

          if (actualHeight > maxHeight) {
            const btn = document.createElement("button");
            btn.textContent = "Show More";
            btn.className = "show-more-btn";
            el.insertAdjacentElement("afterend", btn);

            btn.addEventListener("click", function() {
              el.classList.toggle("expanded");
              btn.textContent = el.classList.contains("expanded") ? "Show Less" : "Show More";
            });
          }
        });
      }
      // Run once for static content (like emply_introduction)
      applyClampText();

      document.addEventListener(\'keydown\', function(event) {
        // ctrl+shift+c
        if (event.ctrlKey && event.shiftKey && event.keyCode === 67) {
          event.preventDefault();
        }
        // ctrl+shift+i
        if (event.ctrlKey && event.shiftKey && event.keyCode === 73) {
          event.preventDefault();
        }
        // ctrl+u
        if (event.ctrlKey && event.keyCode === 85) {
          event.preventDefault();
        }
        // ctrl+s
        if (event.ctrlKey && event.keyCode === 83) {
          event.preventDefault();
        }
        // ctrl+p 
        if (event.ctrlKey && event.keyCode === 80) {
          event.preventDefault();
        }
        // ctrl+e
        if (event.ctrlKey && event.keyCode === 69) {
          event.preventDefault();
        }
      });
      // right click disable
      document.addEventListener(\'contextmenu\', function(event) {
        event.preventDefault();
      });
    </script>
  </body>
</html>';
if(isset($_SESSION['userlogininfo']['LOGINIDA'])){
  include "include/modals/notification/detail_missing.php";
}
?>