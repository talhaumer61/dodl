<?php
// how_it_work
$condition = array ( 
                     'select'       =>	'how_it_work'
                    ,'where'        =>	array( 
                                               'curs_id' 	     => $_GET['curs_id']
                                              ,'curs_status' 	 => 1
                                              ,'is_deleted' 	 => 0
                                            )
                    ,'return_type'	=>	'single'
                  ); 
$COURSES = $dblms->getRows(COURSES, $condition);
echo'
<div class="tab-pane fade show" id="how_it_works">
    <div class="card overview-sec">
        <div class="card-body">';
            if($COURSES){
                echo'
                <h5 class="subs-title text-center">How the Specialization Works</h5>
                <div class="clamp-text" data-lines="10">
                    '.html_entity_decode(html_entity_decode($COURSES['how_it_work'])).'
                </div>';
            }else{
                echo '<div class="alert alert-danger mb-0 text-center"><strong>No data found...!</strong></div>';
            }
            echo'
        </div>
    </div>
</div>';