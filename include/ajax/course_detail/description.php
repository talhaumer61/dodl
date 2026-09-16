<?php
// what_you_learn
// curs_skills
// curs_about
// objectives
// outcomes
// outlines


// COURSE INFO
$condition = array ( 
                     'select'       =>	'ci.objectives, ci.outcomes, ci.outlines, c.curs_skills, c.curs_about, c.what_you_learn'
                    ,'join'         =>  'LEFT JOIN '.COURSES_INFO.' ci ON ci.id_curs = c.curs_id'
                    ,'where'        =>	array( 
                                                 'c.curs_id'        => $_GET['curs_id']
                                                ,'c.curs_status'    => '1'  
                                                ,'c.is_deleted'     => '0'  
                                            )
                    ,'return_type'	=>	'single'
            );
$COURSES = $dblms->getRows(COURSES.' c', $condition, $sql);

$what_you_learn	= json_decode(html_entity_decode($COURSES['what_you_learn']), true);
echo'
<div class="tab-pane fade show active" id="description">
    <div class="card overview-sec">
        <div class="card-body">';
            $sr = 0;
            if($what_you_learn){
                foreach ($what_you_learn as $value) :
                if(!empty($value)){
                    $sr++;
                    if($sr == 1){
                        echo'
                        <h5 class="subs-title text-center">WHAT YOU WILL LEARN</h5>
                        <div class="row"><div class="col"><ul>';
                    }
                    echo '<li>'.$value.'</li>';
                    $sr = 1;

                }
                endforeach;
                if($sr == 1){
                    echo '</ul></div></div><br>';
                }
            }
            if($COURSES['curs_skills']){
                echo'
                <h5 class="subs-title text-center">SKILLS YOU WILL GAIN</h5>
                <div class="row">
                    <div class="col">
                        <ul>';
                            foreach (explode(',',$COURSES['curs_skills']) as $key => $value) {
                                echo '<li>'.$value.'</li>';
                            }
                            echo '
                        </ul>
                    </div>
                </div>';
            }
            if($COURSES['curs_about']){
                echo'
                <h5 class="subs-title">Description</h5>
                <div class="clamp-text" data-lines="5">
                    '.html_entity_decode(html_entity_decode($COURSES['curs_about'])).'
                </div>';
            }
            if($COURSES['objectives']){
                echo'
                <h5 class="subs-title">Learning Objectives</h5>
                <div class="clamp-text" data-lines="5">
                    '.html_entity_decode(html_entity_decode($COURSES['objectives'])).'
                </div>';
            }
            if($COURSES['outcomes']){
                echo'
                <h5 class="subs-title">Learning Outcomes</h5>
                <div class="clamp-text" data-lines="5">
                    '.html_entity_decode(html_entity_decode($COURSES['outcomes'])).'
                </div>';
            }
            if($COURSES['outlines']){
                echo'
                <h5 class="subs-title">'.(CONTROLER == 'trainings' ? 'Sections Details' : 'Weekly Lectures Plan').'</h5>
                <div class="clamp-text" data-lines="5">
                    '.html_entity_decode(html_entity_decode($COURSES['outlines'])).'
                </div>';
            }
            echo'
        </div>
    </div>
</div>';