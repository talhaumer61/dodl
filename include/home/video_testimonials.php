
<style>
    /* ===== Video Testimonials Section ===== */
    .video-section {
        text-align: center;
        padding: 80px 20px;
        /* background: #f9fafc; */
    }
    .video-section h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #222;
        margin-bottom: 10px;
    }
    .video-section p {
        font-size: 1rem;
        color: #666;
        margin-bottom: 50px;
    }

    /* ===== Owl Carousel ===== */
    .video-carousel .video-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
    }
    .video-carousel video {
        width: 100%;
        max-width: 450px;
        height: 260px;
        border-radius: 12px;
        /* box-shadow: 0 3px 15px rgba(0,0,0,0.2); */
        object-fit: cover;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }
    .video-carousel .owl-item.center video {
        transform: scale(1.08);
        /* box-shadow: 0 8px 25px rgba(0,0,0,0.3); */
    }
    .video-carousel .owl-item {
        opacity: 0.6;
        transition: opacity 0.3s ease;
    }
    .video-carousel .owl-item.center {
        opacity: 1;
    }

    /* Pagination Dots */
    .video-carousel .owl-dots {
        margin-top: 30px;
    }
    .video-carousel .owl-dot span {
        width: 35px;
        height: 12px;
        border-radius: 999px;
        background: #ff602e;
        transition: all 0.3s ease;
    }
    .video-carousel .owl-dot.active span {
        width: 12px;
        height: 12px;
        background: #ffe1db;
    }
</style>

<?php 
$condition = array(
                    'select'      => "rev_video",
                    'where'       => array(
                                        'rev_status' => 1,
                                        'is_deleted' => 0
                                    ),
                    'not_equal'   => [
                                        'rev_video' => ''
                                      ],
                    'limit'       => 12,
                    'return_type' => 'all'
                );
$reviews = $dblms->getRows(REVIEWS, $condition);

if (!empty($reviews)) {
echo'
<section class="video-section">
  <div class="container">
    <div class="section-header aos" data-aos="fade-up">
      <div class="section-sub-head feature-head text-center mb-0">
        <h2>What Our Clients Say</h2>
        <div class="section-text aos" data-aos="fade-up">
          <p class="mb-0">
            Real stories from our happy clients — hear their experiences in their own words.
          </p>
        </div>
      </div>
    </div>

    <div class="owl-carousel video-carousel owl-theme aos" data-aos="fade-up">';
      foreach ($reviews as $rev): 
            if (!empty($rev['rev_video'])):
                $videoPath = "uploads/videos/reviews/" . $rev['rev_video'];
                echo'
                <div class="video-card" data-video="'.SITE_URL_PORTAL . $videoPath.'">
                    <video preload="metadata">
                    <source src="'.SITE_URL_PORTAL.$videoPath.'#t=0.1" type="video/mp4">
                    </video>
                </div>';
            endif; 
        endforeach;
        echo'
    </div>
  </div>
</section>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark">
      <div class="modal-body p-0">
        <video id="modalVideo" controls autoplay style="width:100%; border-radius:8px;">
          <source src="" type="video/mp4">
        </video>
      </div>
    </div>
  </div>
</div>';
}
?>

<script>
$(document).ready(function() {
  // Initialize Owl Carousel
  $('.video-carousel').owlCarousel({
    loop: true,          
    margin: 30,
    center: true,
    autoplay: false,     
    dots: true,
    nav: false,
    smartSpeed: 700,
    responsive: {
      0: { items: 1 },
      768: { items: 2 },
      1024: { items: 3 }
    }
  });

  // Modal video logic
  $('.video-card').on('click', function() {
    const videoSrc = $(this).data('video');
    const modalVideo = $('#modalVideo');
    modalVideo.attr('src', videoSrc);
    $('#videoModal').modal('show');

    $('#videoModal').on('hidden.bs.modal', function() {
      modalVideo[0].pause();
      modalVideo.attr('src', '');
    });
  });
});
</script>
