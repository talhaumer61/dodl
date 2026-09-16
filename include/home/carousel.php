<?php
// echo'
// <style>
//   .search_div_big {
//     width: 100%;
//     transition: 2s;
//     z-index: 1;
//   }
//   .search_div_big:hover {
//     height: 10%;
//   }
//   .search_data{
//     max-height: 180px;
//     overflow-y: scroll;
//   }
// </style>
// <section class="home-slide d-flex align-items-center">
//   <div class="container">
//     <div class="row">
//       <div class="col-md-6">
//         <div class="home-slide-face aos" data-aos="fade-up">
//           <div class="home-slide-text mb-3">
//             <h6 class="text-muted">Your Gateway to Lifelong Learning</h6>
//             <h1>'.SITE_NAME.' ('.TITLE_HEADER.')</h1>
//             <h6 class="text-muted">Learn anytime & anywhere <br><br> Empowering your personal and professional growth through our lifelong learning platform</h6>
//           </div>
//           <div class="banner-content" style="position: relative;">
//             <form class="form" autocomplete="off">
//               <div class="form-inner shadow search_div_big">
//                 <div class="input-group">
//                   <i class="fa-solid fa-magnifying-glass search-icon"></i>
//                   <input type="text" class="form-control" id="search_input" placeholder="Search Courses, MasterTracks and Degrees etc" required/>
//                 </div>
//                 <div class="px-2 search_data">
                  
//                 </div>
//               </div>
//             </form>
//           </div>
//         </div>
//       </div>
//       <div class="col-md-6 d-flex align-items-center">
//         <div class="girl-slide-img aos" data-aos="fade-up">
//           <img src="assets/img/object.png" alt />
//         </div>
//       </div>
//     </div>
//   </div>
// </section>
// <script>
//   $(document).ready(function() {
//     $("#search_input").on("keyup", function() {
//       var search_value = $(this).val().trim(); // ✅ trim spaces
//       $.ajax({
//         url: "include/ajax/searchbar.php",
//         method: "POST",
//         data: { search_word: search_value },
//         success: function(response) {

//           //$("#resultContainer").html(response);
//           if (response != "") {
//             $(".search_height").addClass("search_div_big");
//             $(".search_data").html(response);
//           } else {
//             $(".search_height").removeClass("search_div_big");
//           }
//         }
//       });
//     });
//   });
// </script>';
?>

<?php
echo '
<style>
  .search_div_big {
    width: 100%;
    transition: 2s;
    z-index: 1;
  }
  .search_div_big:hover {
    height: 10%;
  }
  .search_data {
    max-height: 180px;
    overflow-y: scroll;
  }
  /* Particle background styling */
  #particles-js {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
  }
  .home-slide {
    position: relative;
    overflow: hidden;
  }
  .home-slide .container {
    position: relative;
    z-index: 2; /* content above particles */
  }
</style>

<section class="home-slide d-flex align-items-center">
  <div id="particles-js"></div>
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
                  <input type="text" class="form-control" id="search_input" placeholder="Search Courses, MasterTracks and Degrees etc" required/>
                </div>
                <div class="px-2 search_data"></div>
              </div>
            </form>
          </div>
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

<script src="'.SITE_URL.'assets/js/particles.min.js"></script>
<script>
  // Initialize particles
  particlesJS("particles-js",
    
    {
      "particles": {
        "number": {
          "value": 80,
          "density": {
            "enable": true,
            "value_area": 800
          }
        },
        "color": {
          "value": "#00bcd4"
        },
        "shape": {
          "type": "circle",
          "stroke": {
            "width": 0,
            "color": "#000000"
          },
          "polygon": {
            "nb_sides": 5
          },
          "image": {
            "src": "img/github.svg",
            "width": 100,
            "height": 100
          }
        },
        "opacity": {
          "value": 0.5,
          "random": false,
          "anim": {
            "enable": false,
            "speed": 1,
            "opacity_min": 0.1,
            "sync": false
          }
        },
        "size": {
          "value": 5,
          "random": true,
          "anim": {
            "enable": false,
            "speed": 40,
            "size_min": 0.1,
            "sync": false
          }
        },
        "line_linked": {
          "enable": true,
          "distance": 150,
          "color": "#00bcd4",
          "opacity": 0.4,
          "width": 1
        },
        "move": {
          "enable": true,
          "speed": 6,
          "direction": "none",
          "random": false,
          "straight": false,
          "out_mode": "out",
          "attract": {
            "enable": false,
            "rotateX": 600,
            "rotateY": 1200
          }
        }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": {
            "enable": true,
            "mode": "repulse"
          },
          "onclick": {
            "enable": true,
            "mode": "push"
          },
          "resize": true
        },
        "modes": {
          "grab": {
            "distance": 400,
            "line_linked": {
              "opacity": 1
            }
          },
          "bubble": {
            "distance": 400,
            "size": 40,
            "duration": 2,
            "opacity": 8,
            "speed": 3
          },
          "repulse": {
            "distance": 200
          },
          "push": {
            "particles_nb": 4
          },
          "remove": {
            "particles_nb": 2
          }
        }
      },
      "retina_detect": true,
      "config_demo": {
        "hide_card": false,
        "background_color": "#b61924",
        "background_image": "",
        "background_position": "50% 50%",
        "background_repeat": "no-repeat",
        "background_size": "cover"
      }
    }

  );

  // AJAX search logic
  $(document).ready(function() {
    $("#search_input").on("keyup", function() {
      var search_value = $(this).val().trim();
      $.ajax({
        url: "include/ajax/searchbar.php",
        method: "POST",
        data: { search_word: search_value },
        success: function(response) {
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