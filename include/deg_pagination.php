<?php
if($count>$Limit) {
    echo'
    <div class="row content-center">
        <div class="col">
            <div class="justify-content-start mb-0 mt-3">
                Showing <b>'.((($zone - 1) * $Limit) + 1).'</b> to <b>'.$srno.'</b> of <b>'.$count.'</b> entries
            </div>
        </div>
        <div class="col">
            <ul class="pagination lms-page lms-pagination">';
                $current_page = CONTROLER;
                $pagination = "";
                if($lastpage >= 1){
                    // PREVIOUS BUTTON
                    if($zone > 0){
                        $pagination.= '<li class="page-item prev '.($zone==1 ? 'disabled' : '').'"><a class="page-link" href="'.SITE_URL.$current_page.'/'.$prev.$sqlstring.((!empty($filters))?'/':'').$filters.'"><i class="fas fa-angle-left"></i></a></li>';
                    }

                    // PAGES 
                    if($lastpage < 7 + ($adjacents * 1)){ //not enough pages to bother breaking it up
                        for($counter = 1; $counter <= $lastpage; $counter++){
                            $pagination.= '<li class="page-item '.($counter == $zone ? 'active' : '').'"><a class="page-link" href="'.($counter == $zone ? '' : SITE_URL.$current_page.'/'.$counter.$sqlstring).((!empty($filters))?'/':'').$filters.'">'.$counter.'</a></li>';
                        }
                    }elseif($lastpage > 5 + ($adjacents * 1)){
                        //enough pages to hide some
                        //close to beginning - only hide later pages
                        if($zone < 1 + ($adjacents * 1)){
                            for($counter = 1; $counter < 4 + ($adjacents * 1); $counter++){
                                $pagination.= '<li class="page-item '.($counter == $zone ? 'active' : '').'"><a class="page-link" href="'.($counter == $zone ? '' : SITE_URL.$current_page.'/'.$counter.$sqlstring).((!empty($filters))?'/':'').$filters.'">'.$counter.'</a></li>';
                            }
                            $pagination.= '<li class="page-item"><a class="page-link disabled"> ... </a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/'.$lpm1.$sqlstring.((!empty($filters))?'/':'').$filters.'">'.$lpm1.'</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/'.$lastpage.$sqlstring.((!empty($filters))?'/':'').$filters.'">'.$lastpage.'</a></li>';   
                        }elseif($lastpage - ($adjacents * 1) > $zone && $zone > ($adjacents * 1)){
                            //in middle; hide some front and some back
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/1'.$sqlstring.((!empty($filters))?'/':'').$filters.'">1</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/2'.$sqlstring.((!empty($filters))?'/':'').$filters.'">2</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/3'.$sqlstring.((!empty($filters))?'/':'').$filters.'">3</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link disabled"> ... </a></li>';
                            for($counter = $zone - $adjacents; $counter <= $zone + $adjacents; $counter++){
                                $pagination.= '<li class="page-item '.($counter == $zone ? 'active' : '').'"><a class="page-link" href="'.($counter == $zone ? '' : SITE_URL.$current_page.'/'.$counter.$sqlstring.((!empty($filters))?'/':'').$filters).'">'.$counter.'</a></li>';
                            }
                            $pagination.= '<li class="page-item"><a class="page-link disabled"> ... </a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/'.$lpm1.$sqlstring.((!empty($filters))?'/':'').$filters.'">'.$lpm1.'</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/'.$lastpage.$sqlstring.((!empty($filters))?'/':'').$filters.'">'.$lastpage.'</a></li>';   
                        }else{
                            //close to end; only hide early pages
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/1'.$sqlstring.((!empty($filters))?'/':'').$filters.'">1</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/2'.$sqlstring.((!empty($filters))?'/':'').$filters.'">2</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link" href="'.SITE_URL.$current_page.'/3'.$sqlstring.((!empty($filters))?'/':'').$filters.'">3</a></li>';
                            $pagination.= '<li class="page-item"><a class="page-link disabled"> ... </a></li>';
                            for($counter = $lastpage - (3 + ($adjacents * 1)); $counter <= $lastpage; $counter++){
                                $pagination.= '<li class="page-item '.($counter == $zone ? 'active' : '').'"><a class="page-link" href="'.($counter == $zone ? '' : SITE_URL.$current_page.'/'.$counter.$sqlstring.((!empty($filters))?'/':'').$filters).'">'.$counter.'</a></li>';
                            }
                        }
                    }

                    // NEXT BUTTON
                    if($zone < $counter){
                        $pagination.= '<li class="page-item next '.($zone==$counter-1 ? 'disabled' : '').'"><a class="page-link" href="'.SITE_URL.$current_page.'/'.$next.$sqlstring.((!empty($filters))?'/':'').$filters.'"><i class="fas fa-angle-right"></i></a></li>';
                    }else{
                        $pagination.= "";
                    }
                    echo $pagination;
                }
                echo'
            </ul>
        </div>
    </div>';
}
?>