<?php 
echo'
<section class="section lead-companies">
  <div class="container">
    <div class="section-header aos" data-aos="fade-up">
      <div class="section-sub-head feature-head text-center">
        <span>Trusted By</span>
        <h2>500+ Leading Universities & Companies</h2>
      </div>
    </div>
    <div class="lead-group aos" data-aos="fade-up">
      <div class="lead-group-slider owl-carousel owl-theme">';
        foreach ($home['companies'] as $key => $company) {
          echo'
          <div class="item">
            <div class="lead-img">
              <img class="img-fluid" alt src="'.$company.'" />
            </div>
          </div>';
        }
        echo'
      </div>
    </div>
  </div>
</section>';
?>