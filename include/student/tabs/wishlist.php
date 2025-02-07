<?php
$condition = array (
                         'select' 		=>	'w.wl_id, w.id_curs, w.id_mas, w.id_ad_prg, c.curs_name, c.curs_photo, c.curs_href, mt.mas_name, mt.mas_photo, mt.mas_href, ap.program, p.prg_name, p.prg_photo, p.prg_href'
                        ,'join' 		=>	'LEFT JOIN '.COURSES.' c ON c.curs_id = id_curs
                                             LEFT JOIN '.MASTER_TRACK.' mt ON mt.mas_id = w.id_mas
                                             LEFT JOIN '.PROGRAMS.' p ON p.prg_id = w.id_ad_prg
                                             LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = w.id_ad_prg' 
                        ,'where' 		=>	array( 
                                                    'w.id_std'    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                ) 
                        ,'order_by'     =>  'w.wl_id DESC'
                        ,'return_type'	=>	'all'
                    ); 
$WISHLIST = $dblms->getRows(WISHLIST.' w', $condition);
echo'
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">            
            <div class="row">';
                if($WISHLIST){
                    foreach ($WISHLIST as $row) {
                        $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                        if($row['prg_name']){
                            $type       = '1';
                            $href       = 'degree-detail/'.$row['prg_href'];
                            $idWish     = $row['id_ad_prg'];
                            $name       = $row['prg_name'];
                            $file_url   = SITE_URL_PORTAL.'uploads/images/programs/'.$row['prg_photo'];
                        }elseif($row['mas_name']){
                            $type       = '2';
                            $href       = 'master-track-detail/'.$row['mas_href'];
                            $idWish     = $row['id_mas'];
                            $name       = $row['mas_name'];
                            $file_url   = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$row['mas_photo'];
                        }elseif($row['curs_name']){
                            $type       = '3';
                            $href       = 'courses/'.$row['curs_href'];
                            $idWish     = $row['id_curs'];
                            $name       = $row['curs_name'];
                            $file_url   = SITE_URL_PORTAL.'uploads/images/courses/'.$row['curs_photo'];
                        }
                        if (check_file_exists($file_url)) {
                            $photo = $file_url;
                        }
                        echo'
                        <div class="col-xl-4 col-lg-4 col-md-6 d-flex">
                            <div class="course-box course-design d-flex">
                                <div class="product">
                                    <div class="product-img">
                                        <a href="'.SITE_URL.$href.'">
                                            <img class="img-fluid" alt="'.$name.'" src="'.$photo.'"/>
                                        </a>
                                    </div>
                                    <div class="product-content">
                                        <div class="head-course-title">
                                            <h3 class="title">
                                                <a href="'.SITE_URL.$href.'">'.$name.'</a>
                                            </h3>
                                        </div>
                                        <div class="rating">
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span class="d-inline-block average-rating"><span>4.0</span> (15)</span>
                                        </div>
                                        <div class="all-btn all-category d-flex align-items-center" >
                                            <form action="'.SITE_URL.'invoice" method="POST">
                                                <input type="hidden" name="url" value="'.CONTROLER.'/'.ZONE.'">
                                                <input type="hidden" name="id" value="'.$idWish.'">
                                                <input type="hidden" name="type" value="'.$type.'">
                                                <button name="enroll" type="submit" class="btn btn-primary">BUY NOW</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                }else{
                    echo '<h4 class="pb-3" align="center">No Wishlist Found</h4>';
                }
                echo'
            </div>
        </div>
    </div>
</div>';
?>