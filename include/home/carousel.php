<?php
echo'
<style>
  .search_div_big {
    width: 100%;
    transition: 2s;
    z-index: 1;
  }
  .search_div_big:hover {
    height: 10%;
  }
  .search_data{
    max-height: 180px;
    overflow-y: scroll;
  }
</style>
<section class="home-slide d-flex align-items-center">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div class="home-slide-face aos" data-aos="fade-up">
          <div class="home-slide-text mb-3">
            <h6 class="text-muted">Your Gateway to Lifelong Learning</h6>
            <h1>'.SITE_NAME.' ('.TITLE_HEADER.')</h1>
            <h6 class="text-muted">Learn anytime & anywhere <br><br> Empowering your personal and professional growth through our lifelong learning platform</h6>
          </div>
          <div class="banner-content" style="position: relative;">
            <form class="form" autocomplete="off">
              <div class="form-inner shadow search_div_big">
                <div class="input-group">
                  <i class="fa-solid fa-magnifying-glass search-icon"></i>
                  <input type="text" class="form-control" id="search_input" placeholder="Search Courses, MasterTracks and Degrees etc" required/>';
                  /*
                  echo'
                  <span class="drop-detail">
                    <select class="form-select select">
                      <option>Category</option>
                      <option>Angular</option>
                      <option>Node Js</option>
                      <option>React</option>
                      <option>Python</option>
                    </select>
                  </span>
                  <button class="btn btn-primary sub-btn" type="submit">
                    <i class="fas fa-arrow-right"></i>
                  </button>';
                  */
                  echo'
                </div>
                <div class="px-2 search_data">
                  
                </div>
              </div>
            </form>
          </div>';
          /*
          echo'
          <div class="trust-user">
            <p>Trusted by over 15K Users <br />worldwide since 2022</p>
            <div class="trust-rating d-flex align-items-center">
              <div class="rate-head">
                <h2><span>1000</span>+</h2>
              </div>
              <div class="rating d-flex align-items-center">
                <h2 class="d-inline-block average-rating">4.4</h2>
                <i class="fas fa-star filled"></i>
                <i class="fas fa-star filled"></i>
                <i class="fas fa-star filled"></i>
                <i class="fas fa-star filled"></i>
                <i class="fas fa-star filled"></i>
              </div>
            </div>
          </div>';
          */
          echo'
        </div>
      </div>
      <div class="col-md-6 d-flex align-items-center">
        <div class="girl-slide-img aos" data-aos="fade-up">
          <img src="assets/img/object.png" alt />
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  $(document).ready(function() {
    $("#search_input").on("keyup", function() {
      var search_value = $(this).val();
      $.ajax({
        url: "include/ajax/searchbar.php",
        method: "POST",
        data: { search_word: search_value },
        success: function(response) {

          //$("#resultContainer").html(response);
          if (response != "") {
            $(".search_height").addClass("search_div_big");
            $(".search_data").html(response);
          } else {
            $(".search_height").removeClass("search_div_big");
          }
        }
      });
    });
  });
</script>';
?>