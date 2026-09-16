<?php
// FAQ'S
$condition = array ( 
                     'select'       =>	'question,answer'
                    ,'where'        =>	array( 
                                               'id_curs' 	     => $_GET['curs_id']
                                              ,'is_deleted' 	 => '0'  
                                            )
                    ,'return_type'	=>	'all'
                  ); 
$COURSES_FAQS = $dblms->getRows(COURSES_FAQS, $condition);
echo'
<div class="tab-pane fade show" id="faq">
    <div class="card overview-sec">
        <div class="card-body">';
            if($COURSES_FAQS){
                echo'
                <h5 class="subs-title text-center">Frequently asked questions</h5>';
                foreach ($COURSES_FAQS as $key => $faq) :
                    echo"
                    <div class='course-card'>
                        <h6 class='cou-title'>
                            <a class='collapsed' data-bs-toggle='collapse' href='#q-".$key."' aria-expanded='false'>".$faq['question']."</a>
                        </h6>
                        <div id='q-".$key."' class='card-collapse collapse px-3'>
                            ".html_entity_decode(html_entity_decode($faq['answer']))."
                        </div>
                    </div>";
                endforeach;
            } else{
                echo '<div class="alert alert-danger mb-0 text-center"><strong>No data found...!</strong></div>';
            }
            echo'
        </div>
    </div>
</div>';