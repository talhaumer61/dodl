<?php
$enrollmentArrary = [
    'Holistic Learning Path'
    ,'Practice Tracker for Skill Building'
    ,'Shareable Certificates'
    ,'Self-Paced Learning Option'
    ,'Course Videos & Readings'
    ,'Practice Quizzes'
    ,'Graded Assignments with Peer Feedback'
    ,'Graded Quizzes with Feedback'
    ,'Graded Assignments'
];
echo'
<div class="tab-pane fade show" id="enrollment">
    <div class="card overview-sec">
        <div class="card-body">
            <h5 class="subs-title">Start Learning Today</h5>
            <ul>';
                foreach ($enrollmentArrary as $key => $value) {
                    echo'<li>'.$value.'</li>';
                }
                echo'
            </ul>
        </div>
    </div>
</div>';