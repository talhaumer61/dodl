<?php 
echo'
<section class="section master-skill">
  <div class="container">
    <div class="row">
      <div class="col-lg-7 col-md-12">
        <div class="section-header aos" data-aos="fade-up">
          <div class="section-sub-head">
            <span>What’s New</span>
            <h2>'.$home['master_skills']['title'].'</h2>
          </div>
        </div>
        <div class="section-text aos" data-aos="fade-up">
          <p>'.$home['master_skills']['detail'].'</p>
        </div>
        <div class="career-group aos" data-aos="fade-up">
          <div class="row">';
            foreach ($home['master_skills']['cards'] as $key => $skill_cards){
              echo'
              <div class="col-lg-6 col-md-6 d-flex">
                <div class="certified-group d-flex">
                  <div class="get-certified d-flex align-items-center shadow">
                    <div class="blur-box">
                      <div class="certified-img">
                        <img src="'.$skill_cards['icon'].'" alt class="img-fluid"/>
                      </div>
                    </div>
                    <p>'.$skill_cards['detail'].'</p>
                  </div>
                </div>
              </div>';
            }
            echo'
          </div>
        </div>
      </div>
      <div class="col-lg-5 col-md-12 d-flex align-items-end">
        <div class="career-img aos" data-aos="fade-up">
          <img src="assets/img/join.png" alt class="img-fluid" />
        </div>
      </div>
    </div>
  </div>
</section>';
?>