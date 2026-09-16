<?php
echo'
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">
            <div class="row">';
                foreach ($courses as $key => $course) {
                    if($course['type'] == 'purchase'){
                        echo'
                        <div class="col-lg-12 col-md-12 d-flex">
                            <div class="course-box course-design list-course d-flex">
                                <div class="product">
                                    <div class="product-img">
                                        <a href="">
                                            <img class="img-fluid" alt src="'.$course['thumbnail'].'" />
                                        </a>
                                        <div class="price">
                                            <h3 class="free-color">'.$course['price'].'</h3>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <div class="head-course-title">
                                            <h3 class="title">
                                                <a href="" >'.$course['title'].'</a >
                                            </h3>
                                            <div class="all-btn all-category d-flex align-items-center">
                                                <a href="" class="btn btn-primary">Invoice</a>
                                            </div>
                                        </div>
                                        <div class="course-info d-flex align-items-center border-bottom-0 pb-0" >
                                            <div class="rating-img d-flex align-items-center">
                                                <img src="assets/img/icon/icon-01.svg" alt />
                                                <p>'.$course['lessons'].'</p>
                                            </div>
                                            <div class="course-view d-flex align-items-center" >
                                                <img src="assets/img/icon/icon-02.svg" alt />
                                                <p>'.$course['duration'].'</p>
                                            </div>
                                        </div>
                                        <div class="rating">
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span class="d-inline-block average-rating"><span>4.0</span> (15)</span>
                                        </div>
                                        <div class="course-group d-flex mb-0">
                                            <div class="course-group-img d-flex">
                                                <a href="" ><img src="'.$course['university_logo'].'" alt class="img-fluid"/></a>
                                                <div class="course-name">
                                                    <h4><a href="">'.$course['university'].'</a></h4>
                                                    <p>University</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                }
                echo'
            </div>
        </div>
    </div>
</div>';
?>