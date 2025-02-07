<?php
echo'
<div '.(isset($_SESSION['QUIZ_STARTTED'])?'':'class="main_content mb-5"').'>';
    // IF STUDENT WANT TO SEE COURSE LESSONS
    if ($view === 'lesson') {
        echo'
        <div class="relative z-10">';
            //COURSES LESSONS                    
            $con = array(
                             'select'       => 'l.lesson_id, l.id_week, l.id_lecture, l.lesson_topic, l.lesson_content, l.lesson_video_code, l.lesson_detail, l.lesson_reading_detail, lt.is_completed, lt.my_note_pad, lt.std_review'
                            ,'join'         => 'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_lecture = l.lesson_id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                            ,'where'        => array(
                                                         'l.lesson_status'    => 1
                                                        ,'l.is_deleted'       => 0
                                                        ,'l.lesson_id'        => cleanvars($slug)
                                                    )
                            ,'return_type'  => 'single'
                        );
            $COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS.' AS l',$con);

            // FOR LOAD IF THE LESSON HAS THE VIDEO ID
            if ($COURSES_LESSONS['lesson_content'] != 2) {
                $videoId = '1017859421';
                $accessToken = '29b0177a60abe210225ef28c9904af57'; // Replace with your access token

                $videoData = file_get_contents("https://api.vimeo.com/videos/$videoId", false, stream_context_create([
                    "http" => [
                        "header" => "Authorization: bearer $accessToken"
                    ]
                ]));

                $videoInfo = json_decode($videoData, true);
                $videoUrl = $videoInfo['player_embed_url']; // Get the direct video link
                echo'
                <div class="embed-video">
                    <script src="https://player.vimeo.com/api/player.js"></script>
                    <iframe 
                        src="'.$videoUrl.'?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479&amp;title=0&amp;byline=0&amp;portrait=0&amp;dnt=1" 
                        frameborder="0"
                        allow="autoplay; 
                        fullscreen; 
                        picture-in-picture; 
                        clipboard-write" 
                        style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    </iframe>';
                    
                    // LESSON TRACKING PLAYER AND JAVASCRIPT CODE
                    if ($COURSES_LESSONS['is_completed'] != 2) {
                        include ('get_time_and_track_lecture.php');
                    }
                    echo'                 
                </div>';
            } else {
                // LESSON TRACKING FOR THE READING METRAIL
                if ($COURSES_LESSONS['is_completed'] != 2) {
                    echo'
                    <script>
                        setTimeout(function() {
                            var id_week             = "'.$COURSES_LESSONS['id_week'].'";
                            var id_lecture          = "'.$COURSES_LESSONS['lesson_id'].'";
                            var id_curs             = "'.$COURSES['curs_id'].'";
                            var id_mas              = "'.$COURSES['id_mas'].'";
                            var id_ad_prg           = "'.$COURSES['id_ad_prg'].'";
                            var curs_href           = "'.$COURSES['curs_href'].'";
                            var videoRemainingTime  = document.getElementById("video_remaining_time");
                            $.ajax({
                                url        : "'.SITE_URL.'include/ajax/get_tracking.php"
                                ,method     : "POST"
                                ,data       : {
                                                 "id_week"      : id_week
                                                ,"id_lecture"   : id_lecture
                                                ,"id_curs"      : id_curs
                                                ,"id_mas"       : id_mas
                                                ,"id_ad_prg"    : id_ad_prg
                                                ,"curs_href"    : curs_href
                                                ,"track_mood"   : "reading_metrail"
                                }
                                ,success    : function(e) {
                                    if (e == "reading_metrail_completed") {

                                    } else {
                                        videoRemainingTime.innerHTML = e;
                                    }
                                }
                            });
                        }, 10000);
                    </script>';
                }
            }
            echo'
            <div class="lg:px-6 border-b">
                <div class="lg:py-3 mx-auto">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-row align-items-center">
                            <h4 class="text-xl font-semibold mb-0"> 
                                About This Lesson 
                            </h4>
                        </div>
                        <div class="text-center font-semibold mt-1">
                            <h4 class="text-lg font-semibold mb-0" id="video_remaining_time">';
                                // WHEN LESSON IS COMPLETED NEXT BTN
                                if ($COURSES_LESSONS['is_completed'] == 2 && $next_id != '') {
                                    echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success">Next '.moduleName($type).': '.html_entity_decode($name).'</a>';
                                }
                                echo'
                            </h4>
                        </div>
                    </div>
                </div>
            </div>';
            if(!empty($COURSES_LESSONS['lesson_reading_detail'])){
                echo'<div class="lg:px-6 mt-2">'.html_entity_decode(html_entity_decode($COURSES_LESSONS['lesson_reading_detail'])).'</div>';
            }
            echo'
            <nav class="cd-secondary-nav border-b md:p-0 lg:px-6 bg-white " uk-sticky="cls-active:shadow-sm ; media: @s">
                <ul uk-switcher="connect: #lesson-tabs; animation: uk-animation-fade">
                    <li><a class="lg:px-2">Topic Description </a></li>
                    <li><a class="lg:px-2">Topic Resources</a></li>
                    <li><a class="lg:px-2">Discussion Board</a></li>
                    <li><a class="lg:px-2">Note Book</a></li> 
                    <li><a class="lg:px-2">Announcements</a></li>
                    <li><a class="lg:px-2">Q&A</a></li>
                    <li><a class="lg:px-2">Review</a></li> 
                </ul>
            </nav>
            <div class="lg:px-6">
                <div class="lg:py-6 mx-auto uk-switcher" id="lesson-tabs">
                    <!-- Topic Description -->
                    <div>
                        <div class="row">
                            <div class="col">';
                                if(!empty($COURSES_LESSONS['lesson_detail'])){
                                    echo html_entity_decode(html_entity_decode($COURSES_LESSONS['lesson_detail']));
                                }else{
                                    echo '<h6 class="text-danger">No Lesson Description...!</h6>';
                                }
                                echo'
                            </div>
                        </div>
                    </div>

                    <!-- Topic Resources -->
                    <div>
                        <div class="row">';
                            $con = array(
                                            'select'       =>  'cd.id, cd.file_name, cd.url, cd.file'
                                            ,'where'        =>  array(
                                                                         'cd.id_curs'        => cleanvars($COURSES['curs_id'])
                                                                        ,'cd.id_lesson'      => cleanvars($COURSES_LESSONS['lesson_id'])
                                                                        ,'cd.status'         => 1
                                                                        ,'cd.is_deleted'     => 0    
                                                                    )
                                            ,'order_by'     =>  'cd.id ASC'
                                            ,'return_type'  =>  'all'
                                        );
                            $COURSES_DOWNLOADS = $dblms->getRows(COURSES_DOWNLOADS.' AS cd',$con, $sql);
                            if($COURSES_DOWNLOADS){
                                foreach ($COURSES_DOWNLOADS AS $Cdkey => $CdVal) {
                                    if(!empty($CdVal['url'])){
                                        echo'
                                        <div class="col-md-4">
                                            <div class="card p-3 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex flex-row align-items-center">
                                                        <div class="ms-2 c-details">
                                                            <h6 class="mb-0"><a href="'.$CdVal['url'].'" target="_blank">'.moduleName($CdVal['file_name']).'</a></h6>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <div class="text-center">
                                                            <a href="'.$CdVal['url'].'" target="_blank"><i class="fa fa-link" style="font-size: 20px;"></i></a> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                                    }
                                    if(!empty($CdVal['file'])){
                                        echo'
                                        <div class="col-md-4">
                                            <div class="card p-3 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex flex-row align-items-center">
                                                        <div class="ms-2 c-details">
                                                            <h6 class="mb-0"><a href="'.SITE_URL_PORTAL.'uploads/files/lesson_plan/'.$CdVal['file'].'" target="_blank">'.moduleName($CdVal['file_name']).'</a></h6>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <div class="text-center">
                                                            <a href="'.SITE_URL_PORTAL.'uploads/files/lesson_plan/'.$CdVal['file'].'" target="_blank"><i class="fa fa-download" style="font-size: 20px;"></i></a> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                                    }
                                }
                            }else{
                                echo '<h6 class="text-danger">No Course Resources...!</h6>';
                            }
                            echo'
                        </div>
                    </div>

                    <!-- Discussion Board  -->
                    <div>
                        <div class="row">';
                            //COURSES DISCUSSION
                            $con = array(
                                             'select'       =>  'cd.discussion_id, cd.discussion_subject, cd.discussion_detail, cd.id_lecture, cds.dst_detail, a.adm_photo, a.adm_fullname, e.emply_gender'
                                            ,'join'         =>  'INNER JOIN '.ADMINS.' a on a.adm_id = cd.id_added
                                                                 LEFT JOIN '.EMPLOYEES.' e on e.emply_id = cd.id_teacher
                                                                 LEFT JOIN '.COURSES_DISCUSSIONSTUDENTS.' cds on cds.id_discussion = cd.discussion_id'
                                            ,'where'        =>  array(
                                                                         'cd.id_curs'               =>  cleanvars($COURSES['curs_id'])
                                                                        ,'cd.discussion_status'     =>  1
                                                                        ,'cd.is_deleted'            =>  0
                                                                    )
                                            ,'search_by'    =>  ' AND FIND_IN_SET('.$COURSES_LESSONS['id_lecture'].',cd.id_lecture)'
                                            ,'return_type'  =>  'all'
                                        );
                            $COURSES_DISCUSSION = $dblms->getRows(COURSES_DISCUSSION.' cd',$con, $sql);
                            if($COURSES_DISCUSSION){
                                include 'include/discussion_board/query.php';
                                foreach ($COURSES_DISCUSSION AS $Cdkey => $CdVal) {
                                    if($CdVal['emply_gender'] == '2'){
                                        $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
                                    }else{            
                                        $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
                                    }
                                    if(!empty($CdVal['adm_photo'])){
                                        $file_url = SITE_URL_PORTAL.'uploads/images/admin/'.$CdVal['adm_photo'];
                                        if (check_file_exists($file_url)) {
                                            $photo = $file_url;
                                        }
                                    }
                                    echo'
                                    <div class="col-md-12">
                                        <div class="card p-3 mb-3">
                                            <div class="d-flex justify-content-end">';
                                                /*
                                                echo'
                                                <div class="d-flex flex-row align-items-center">
                                                    <div class="icon">
                                                        <img src="'.$photo.'" alt class="shadow w-12 h-12 icon">
                                                    </div>
                                                    <div class="ms-2 c-details">
                                                        <h6 class="mb-0">'.moduleName($CdVal['adm_fullname']).'</h6>
                                                        <b>Intructor</b>
                                                    </div>
                                                </div>';
                                                */
                                                echo'
                                                <div class="mt-2">
                                                    <div class="text-center">';
                                                        if (empty($CdVal['dst_detail'])) {
                                                            echo'<a class="btn btn-sm btn-dark text-white" onclick="show_modal(\''.SITE_URL.'include/modals/learn/discussion_board/add.php?id_discussion='.cleanvars($CdVal['discussion_id']).'\');">Submit Your Discussion</a>';
                                                        } else {
                                                            echo'<span class="btn btn-sm btn-success text-white">Submited</span>';
                                                        }
                                                        echo'
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <h4 class="leading-8 text-xl">'.moduleName($CdVal['discussion_subject']).'</h4>
                                                <p>'.html_entity_decode(html_entity_decode($CdVal['discussion_detail'])).'</p>
                                            </div>
                                        </div>
                                    </div>';
                                }                    
                            }else{
                                echo '<h6 class="text-danger">No Discussion Board...!</h6>';
                            }
                            echo '
                        </div>
                    </div>
                    
                    <!-- Note BooK -->
                    <div>
                        <div class="row">
                            <div class="form-group">
                                <label> Make Your Own Notes</label>
                                <textarea class="form-control" rows="10" id="myNotePad">'.html_entity_decode(html_entity_decode($COURSES_LESSONS['my_note_pad'])).'</textarea>
                            </div>
                            <script>
                            $(document).ready(function() {
                                $("#myNotePad").on("input", function() {
                                    var myNote = $(this).val().trim();
                                    if (myNote !== "") {
                                        var id_week             = "'.$COURSES_LESSONS['id_week'].'";
                                        var id_lecture          = "'.$COURSES_LESSONS['lesson_id'].'";
                                        var id_curs             = "'.$COURSES['curs_id'].'";
                                        var curs_href           = "'.$COURSES['curs_href'].'";
                                        var id_mas              = "'.$COURSES['id_mas'].'";
                                        var id_ad_prg           = "'.$COURSES['id_ad_prg'].'";
                                        var id_std              = "'.$_SESSION['userlogininfo']['STDID'].'";
                                        $.ajax({
                                             url        : "'.SITE_URL.'include/ajax/get_tracking.php"
                                            ,method     : "POST"
                                            ,data       : {
                                                             "id_week"      : id_week
                                                            ,"id_lecture"   : id_lecture
                                                            ,"id_curs"      : id_curs
                                                            ,"curs_href"    : curs_href
                                                            ,"id_mas"       : id_mas
                                                            ,"id_ad_prg"    : id_ad_prg
                                                            ,"myNote"       : myNote
                                                            ,"id_std"       : id_std
                                                            ,"track_mood"   : "my_note_pad_saved"
                                            }
                                        });
                                    }
                                });
                            });
                            </script>
                        </div>
                    </div>

                    <!-- Announcements -->
                    <div>
                        <div class="row">';
                            //COURSES ANNOUNCEMENTS
                            $con    = array(
                                                 'select'       =>  'ca.announcement_topic, ca.announcement_detail, ca.id_lecture, ca.date_added, a.adm_photo, a.adm_fullname, e.emply_gender'
                                                ,'join'         =>  'INNER JOIN '.ADMINS.' a on a.adm_id = ca.id_added
                                                                     LEFT JOIN '.EMPLOYEES.' e on e.emply_id = ca.id_teacher'
                                                ,'where'        =>  array(
                                                                             'ca.id_curs'                => cleanvars($COURSES['curs_id'])
                                                                            ,'ca.announcement_status'    => 1
                                                                            ,'ca.is_deleted'             => 0
                                                                        )
                                                ,'return_type'  =>  'all'
                                            );
                            $COURSES_ANNOUNCEMENTS  = $dblms->getRows(COURSES_ANNOUNCEMENTS.' ca',$con);
                            if($COURSES_ANNOUNCEMENTS){
                                foreach ($COURSES_ANNOUNCEMENTS AS $Cakey => $CaVal) {
                                    // CHECK FILE EXIST
                                    if($CaVal['emply_gender'] == '2'){
                                        $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
                                    }else{            
                                        $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
                                    }
                                    if(!empty($CaVal['adm_photo'])){
                                        $file_url   = SITE_URL_PORTAL.'uploads/images/admin/'.$CaVal['adm_photo'];
                                        if (check_file_exists($file_url)) {
                                            $photo = $file_url;
                                        }
                                    }
                                    echo'
                                    <div class="col-md-12">
                                        <div class="card p-3 mb-2">
                                            <div class="d-flex justify-content-end">';
                                                /*
                                                echo'
                                                <div class="d-flex flex-row align-items-center">
                                                    <div class="icon">
                                                        <img src="'.$photo.'" alt class="rounded-full shadow w-12 h-12">
                                                    </div>
                                                    <div class="ms-2 c-details">
                                                        <h6 class="mb-0">'.moduleName($CaVal['adm_fullname']).'</h6>
                                                        <b>Intructor</b>
                                                    </div>
                                                </div>';
                                                */
                                                echo'
                                                <div class="mt-2">
                                                    <div class="text-center">
                                                        Date: <b class="text-info"> '.date("d M,Y",strtotime($CaVal['date_added'])).' </b>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <h4 class="leading-8 text-xl">'.moduleName($CaVal['announcement_topic']).'</h4>
                                                <p>'.html_entity_decode(html_entity_decode($CaVal['announcement_detail'])).'</p>
                                            </div>
                                        </div>
                                    </div>';
                                }                    
                            }else{
                                echo '<h6 class="text-danger">No Announcements...!</h6>';
                            }
                            echo '
                        </div>
                    </div>

                    <!-- Q&A -->
                    <div>
                        <div class="row">                        
                            <div class="col">
                                <div class="card border-0">
                                    <div class="card-header bg-success">
                                        <h6 class="mb-0 text-white">Ask question to teacher</h6>
                                    </div>
                                    <div class="chat-messages card-body">
                                        <div class="message">
                                            <div class="receiver">
                                                Hello! How can I help you today?
                                            </div>
                                        </div>';
                                        $condition = array(
                                                            'select'       => 'qa.id, qa.status, qa.type, qa.message, qa.datetime_sent'
                                                            ,'where'        => array(
                                                                                        'qa.id_curs'       =>  cleanvars($COURSES['curs_id'])
                                                                                        ,'qa.is_deleted'    =>  0
                                                                                    )
                                                            ,'search_by'    => ' AND (qa.id_user = '.$_SESSION['userlogininfo']['STDID'].' || qa.reply_to = '.$_SESSION['userlogininfo']['STDID'].')'
                                                            ,'order_by'     => 'qa.datetime_sent ASC'
                                                            ,'return_type'  => 'all'
                                                        );
                                        $QUESTION_ANSWERS = $dblms->getRows(QUESTION_ANSWERS.' qa', $condition);
                                        if($QUESTION_ANSWERS){
                                            foreach ($QUESTION_ANSWERS as $keyQA => $valQA) {
                                                echo'
                                                <div class="message">
                                                    <div class="'.($valQA['type'] == 1 ? 'alert-success sender' : 'receiver shadow').'">
                                                        '.$valQA['message'].'
                                                    </div>
                                                    <div class="message-time-'.($valQA['type'] == 1 ? 'right' : 'left').'">'.date('d M, Y h:i A', strtotime($valQA['datetime_sent'])).' - '.get_msg_status($valQA['status']).'</div>
                                                </div>';
                                            }
                                        }
                                        echo'
                                    </div>
                                    <div class="chat-input card-footer">
                                        <input type="text" placeholder="Type a message..." id="std_message">
                                        <button class="btn btn-success" id="send_message"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
                                
                                    // Function to load messages
                                    function loadMessages() {
                                        $(".chat-messages").load(location.href + " .chat-messages > *", function() {
                                            // $(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
                                        });
                                    }

                                    // Set an interval to refresh messages every 10 seconds
                                    setInterval(loadMessages, 10000); // 10000 milliseconds = 10 seconds
                                    
                                    loadMessages();

                                    $("#send_message").click(function() { 
                                        var std_message = $("#std_message").val().trim();
                                        if (std_message !== "") {
                                            var id_lecture = "'.$COURSES_LESSONS['lesson_id'].'";
                                            var id_curs = "'.$COURSES['curs_id'].'";
                                            var id_std = "'.$_SESSION['userlogininfo']['STDID'].'";
                                            $.ajax({
                                                type: "POST",
                                                url: "'.SITE_URL.'include/ajax/get_tracking.php",
                                                data: {
                                                    "id_lecture"    : id_lecture,
                                                    "id_curs"       : id_curs,
                                                    "std_message"   : std_message,
                                                    "id_std"        : id_std,
                                                    "_method"       : "student_teacher_qna"
                                                },
                                                success: function(response) {
                                                    console.log(response);
                                                    $(".chat-messages").append(response);
                                                    $(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
                                                    $("#std_message").val("");
                                                    // $(".chat-messages").load(location.href + " .chat-messages");
                                                }
                                            });
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                    
                    <!-- Review -->
                    <div>
                        <div class="row">
                            <div class="form-group">
                                <label> Review about the Lecture Topic</label>
                                <label class="text-danger"> (Once you write your review it will be saved automatically)</label>
                                <textarea class="form-control" rows="10" id="std_review">'.html_entity_decode(html_entity_decode($COURSES_LESSONS['std_review'])).'</textarea>
                            </div>
                            <script>
                            $(document).ready(function() {
                                $("#std_review").on("input", function() {
                                    var std_review = $(this).val().trim();
                                    if (std_review !== "") {
                                        var id_week             = "'.$COURSES_LESSONS['id_week'].'";
                                        var id_lecture          = "'.$COURSES_LESSONS['lesson_id'].'";
                                        var id_curs             = "'.$COURSES['curs_id'].'";
                                        var curs_href           = "'.$COURSES['curs_href'].'";
                                        var id_mas              = "'.$COURSES['id_mas'].'";
                                        var id_ad_prg           = "'.$COURSES['id_ad_prg'].'";
                                        var id_std              = "'.$_SESSION['userlogininfo']['STDID'].'";
                                        $.ajax({
                                             url        : "'.SITE_URL.'include/ajax/get_tracking.php"
                                            ,method     : "POST"
                                            ,data       : {
                                                             "id_week"      : id_week
                                                            ,"id_lecture"   : id_lecture
                                                            ,"id_curs"      : id_curs
                                                            ,"curs_href"    : curs_href
                                                            ,"id_mas"       : id_mas
                                                            ,"id_ad_prg"    : id_ad_prg
                                                            ,"std_review"   : std_review
                                                            ,"id_std"       : id_std
                                                            ,"track_mood"   : "std_review_saved"
                                            }
                                        });
                                    }
                                });
                            });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
    // IF STUDENT WANT TO SEE COURSE ASSIGNMENTS
    else if ($view === 'assignments') {
        echo'
        <div class="relative z-10">';
            //COURSES ASSIGNMENTS
            $con = array(
                             'select'       => 'ca.id,ca.caption,ca.detail,ca.fileattach,ca.date_start,ca.date_end,e.emply_name,cas.student_file,ca.id_week,ca.id_curs,lt.is_completed'
                            ,'join'         => 'LEFT JOIN '.EMPLOYEES.' e ON e.emply_id = ca.id_teacher 
                                                LEFT JOIN '.COURSES_ASSIGNMENTS_STUDENTS.' cas ON ca.id = cas.id_assignment
                                                LEFT JOIN '.LECTURE_TRACKING.' lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_assignment = ca.id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                            ,'where'        => array(
                                                         'ca.id_curs'       => cleanvars($COURSES['curs_id'])
                                                        ,'ca.status'        => 1
                                                        ,'ca.is_deleted'    => 0
                                                        ,'ca.id'            => cleanvars($slug)
                                                    )
                            ,'return_type'  => 'single'
            );
            $COURSES_ASSIGNMENTS = $dblms->getRows(COURSES_ASSIGNMENTS.' ca',$con);
            // LOADING THE ASSIGNMENT FILE BUTTON
            if ($COURSES_ASSIGNMENTS['lesson_content'] != 2) {
                $extension      = pathinfo($COURSES_ASSIGNMENTS['fileattach'],PATHINFO_EXTENSION);
                $officeFiles    = ['xlsx','xls','doc','docx','ppt','pptx'];
                $pdfFiles       = ['pdf'];
                $imgFiles       = ['png','jpg','jpeg'];

                // WORD, PPT, EXCEL
                if (in_array($extension,$officeFiles)) {
                    echo'
                    <!--
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://786electronic.com/include/hamza.pptx"></iframe>
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://786electronic.com/include/hamza.xlsx"></iframe>
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://786electronic.com/include/hamza.docx"></iframe>
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src='.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>
                    -->
                    <div class="embed-video">
                        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src='.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>
                    </div>';
                } 
                // PDF
                else if (in_array($extension,$pdfFiles)) {    
                    echo'
                    <div class="embed-video">
                        <iframe src="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>
                    </div>';
                } 
                // IMAGE
                else if (in_array($extension,$imgFiles)) {
                    echo'
                    <div class="embed-video">
                        <img src="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'" alt="'.$COURSES_ASSIGNMENTS['caption'].'" class="img-fluid">
                    </div>';
                }
            } 
            echo'                    
            <div class="lg:px-6">
                <div class="lg:py-6 mx-auto">
                    <div class="row">                            
                        <div class="flex-row align-items-center mb-3">
                            <h4 class="text-xl font-semibold mb-0"> '.html_entity_decode($COURSES_ASSIGNMENTS['caption']).'</h4>
                            <div>'.html_entity_decode(html_entity_decode($COURSES_ASSIGNMENTS['detail'])).'</div>
                        </div>
                        <hr>
                        <div class="col-md-12 mb-3">
                            <div class="text-center">';
                                // ASSIGNMENT FILE DOWNLOAD
                                if (!empty($COURSES_ASSIGNMENTS['fileattach'])) {
                                    echo'<a rel="nofollow" download="'.$COURSES_ASSIGNMENTS['fileattach'].'" target="_blank" title="Download Assignment File" href="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'" class="btn btn-sm btn-primary text-white me-1"><i class="fa fa-download me-1"></i>Assignment File</a>';
                                }
                                // STUDENT UPLOADED FILE 
                                if (!empty($COURSES_ASSIGNMENTS['student_file'])) {
                                    echo'<a rel="nofollow" download="'.$COURSES_ASSIGNMENTS['student_file'].'" target="_blank" title="Download Submited File" href="'.SITE_URL.'uploads/files/student_assignments/'.$COURSES_ASSIGNMENTS['student_file'].'" class="btn btn-sm btn-dark text-white me-1"><i class="fa fa-download me-1"></i>Your File</a>';
                                    // echo'<span class="btn btn-sm btn-success text-white me-1">Assignment Submited</span>';
                                }else{
                                    include 'include/assignments/query.php';
                                    echo'<a class="btn btn-sm btn-success text-white me-1" onclick="show_modal(\''.SITE_URL.'include/modals/learn/assignments/add.php?id_week='.cleanvars($COURSES_ASSIGNMENTS['id_week']).'&id_curs='.cleanvars($COURSES['curs_id']).'&id_mas='.cleanvars($COURSES['id_mas']).'&id_ad_prg='.cleanvars($COURSES['id_ad_prg']).'&id_assignment='.cleanvars($slug).'&id_lecture='.cleanvars($COURSES_ASSIGNMENTS['id']).'&caption='.$COURSES_ASSIGNMENTS['caption'].'\');"><i class="fa fa-upload me-1"></i>Submit Assignment</a>';
                                }
                                // WHEN LESSON, ASSIGNMENT OR QUIZ IS COMPLETED THEN NEXT BTN
                                if ($COURSES_ASSIGNMENTS['is_completed'] == 2 && $next_id != '') {
                                    echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success">Next '.moduleName($type).': '.$name.'</a>';
                                }
                                echo'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
    // IF STUDENT WANT TO SEE COURSE QUIZ
    else if ($view === 'quiz') {
        echo'
        <div class="relative z-10">';
            //COURSES QUIZ
            $con    = array(
                             'select'       =>  'q.quiz_id, q.quiz_title, q.quiz_instruction, q.quiz_time, q.quiz_totalmarks, q.quiz_passingmarks, q.quiz_no_qns, q.id_week, q.id_curs, qs.qzstd_id
                                                ,COUNT(CASE WHEN qq.quiz_qns_type = 3 THEN qq.quiz_qns_id ELSE NULL END) AS countMultipleChoice
                                                ,COUNT(CASE WHEN qq.quiz_qns_type = 1 THEN qq.quiz_qns_id ELSE NULL END) AS countShort
                                                ,SUM(CASE WHEN qq.quiz_qns_type = 3 THEN qq.quiz_qns_marks ELSE NULL END) AS marksMultipleChoice
                                                ,SUM(CASE WHEN qq.quiz_qns_type = 1 THEN qq.quiz_qns_marks ELSE NULL END) AS marksShort
                                                ,qs.qzstd_obtain_marks, qs.qzstd_pass_fail, qs.qzstd_id'
                            ,'join'         =>  'INNER JOIN '.QUIZ_QUESTIONS.' qq ON qq.id_quiz = q.quiz_id
                                                 LEFT JOIN '.QUIZ_STUDENTS.' qs ON ( qs.id_quiz = q.quiz_id AND qs.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).')'
                            ,'where'        =>  array(
                                                         'q.quiz_status'  => 1
                                                        ,'q.is_publish'   => 1
                                                        ,'q.is_deleted'   => 0
                                                        ,'q.quiz_id'      => cleanvars($slug)
                                                    )
                            ,'return_type'  => 'single'
                        );
            $QUIZ = $dblms->getRows(QUIZ.' q',$con);
            if (isset($_SESSION['QUIZ_STARTTED'])) {
                echo'
                <form method="POST" id="quizFrom" class="quizFrom">
                    <script>
                        document.addEventListener("keydown", function (e) {
                            if (e.ctrlKey && e.key === "r") {
                                e.preventDefault();
                            }
                            if (e.ctrlKey && e.key === "e") {
                                e.preventDefault();
                            }
                            if (e.ctrlKey && e.key === "F5") {
                                e.preventDefault();
                            }
                        });
                    </script>
                    <div class="lg:px-6">
                        <div class="lg:py-6 mx-auto">
                            <style>
                                .sticky-top {
                                    position: fixed;
                                    top: 0;
                                    left: 0;
                                    right: 0;
                                    z-index: 1000;
                                    background-color: white;
                                    padding: 10px;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                }
                                p{
                                    margin-bottom: 5px;
                                }
                            </style>
                            <div id="quizBar" class="sticky-top d-flex justify-content-between">
                                <div class="d-flex flex-row align-items-center">
                                    <h4 class="text-xl font-semibold mb-0"> '.moduleName($QUIZ['quiz_title']).'</h4>
                                    <input type="hidden" value="'.cleanvars($QUIZ['quiz_id']).'" name="id_quiz">
                                    <input type="hidden" value="'.cleanvars($QUIZ['id_week']).'" name="id_week">
                                    <input type="hidden" value="'.cleanvars($QUIZ['id_curs']).'" name="id_curs">
                                    <input type="hidden" value="end" name="quiz_method">
                                </div>
                                <div>
                                    <h3>
                                        <span style="font-size: 20px;" class="fa fa-clock"></span> <span style="font-size: 20px;" id="quizTimmer">00:00:00</span>
                                        <script>
                                            function SecToTime(seconds) {
                                                const hours = Math.floor(seconds / 3600);
                                                const minutes = Math.floor((seconds % 3600) / 60);
                                                const remainingSeconds = seconds % 60;
                                                var time = "";
                                                if (hours == 0) {
                                                    time = `${minutes}:${remainingSeconds}`;
                                                } else if (minutes == 0) {
                                                    time = `${remainingSeconds} seconds`;
                                                } else {
                                                    time = `${hours}:${minutes}:${remainingSeconds}`;
                                                }
                                                return time;
                                            }
                                            function MinToSec(minutes) {
                                                return minutes * 60;
                                            }
                                            const countdownDuration = 1;
                                            var secondsRemaining    = MinToSec("'.$QUIZ['quiz_time'].'");
                                            function quizTimmer() {
                                                var quizTimmer  = document.getElementById("quizTimmer");
                                                if (secondsRemaining > countdownDuration) {
                                                    secondsRemaining--;
                                                    quizTimmer.innerHTML    = SecToTime(secondsRemaining);
                                                } else {     
                                                    quizTimmer.innerHTML    = "Time Is Up";
                                                    var quizFrom = $("#quizFrom").serialize();
                                                    $.ajax({
                                                         url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                                        ,method     : "POST"
                                                        ,data       : quizFrom
                                                        ,success    : function() {
                                                            location.reload();
                                                        }
                                                    });
                                                }
                                            }
                                            setInterval(quizTimmer, 1000);
                                        </script>
                                        <button id="quizCancel" class="btn btn-danger btn-sm"><i class="fa fa-close me-1"></i>Cancel</button>
                                        <script>
                                            $("#quizCancel").on("click",function(){
                                                $.ajax({
                                                     url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                                    ,method     : "POST"
                                                    ,data       : {
                                                        "quiz_method" : "cancel"
                                                    }
                                                    ,success    : function() {
                                                        location.reload();
                                                    }
                                                });
                                            });
                                        </script>
                                    </h3>
                                </div>
                            </div>
                            <hr>
                            <div class="space-y-4 mt-5">';
                                if ($_SESSION['QUIZ_STARTTED']) {
                                    $m_flag = 0; 
                                    $s_flag = 0;
                                    foreach ($_SESSION['QUIZ_STARTTED'] AS $key => $val) {
                                        $optionFlag = 0;
                                        if ($val['quiz_qns_type'] == 3) {
                                            $m_flag++;
                                            if ($m_flag == 1) {
                                                echo'
                                                <div class="card">
                                                    <div class="card-header alert-dark">
                                                        <b>Multiple Choice Questions</b>
                                                    </div>
                                                    <div class="card-body py-0">';
                                            }
                                            echo'
                                            <div class="p-2 bg-light border rounded my-3">
                                                <p><b>Q '.($key+1).'):</b></p>
                                                '.entityDecode($val['quiz_qns_question'],5).'   
                                                <div class="p-1">
                                                    <div class="row">';
                                                        foreach ($val['quiz_qns_option'] AS $okey => $oval) {
                                                            $optionFlag++;
                                                            echo'
                                                            <div class="radio col bg-white m-1 border rounded">
                                                                <input type="radio" id="o_'.$key.'_'.$optionFlag.'" name="'.to_seo_url(strip_tags(entityDecode($val['quiz_qns_question'],5))).'" value="'.($okey).'">
                                                                <label for="o_'.$key.'_'.$optionFlag.'" style="font-size: 12px;">
                                                                    <span class="radio-label"></span> 
                                                                    '.($okey == 0?'a':($okey == 1?'b':($okey == 2?'c':'d'))).'): '.$oval['qns_option'].'
                                                                </label>
                                                            </div>
                                                            '.(($okey%2) == 1?'</div><div class="row">':'').'';
                                                        }
                                                        echo'
                                                    </div>
                                                </div>
                                            </div>';
                                        } else if($val['quiz_qns_type'] == 1) {
                                            $s_flag++;
                                            if ($s_flag == 1) {
                                                echo'
                                                </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header alert-dark">
                                                        <b>Short Questions</b>
                                                    </div>
                                                    <div class="card-body py-0">';
                                            }
                                            echo'
                                            <div class="p-2 my-3 border rounded bg-light">                                                    
                                                <p><b>Q '.($key+1).'):</b></p>
                                                '.entityDecode($val['quiz_qns_question'],5).'
                                                <div class="p-1">
                                                    <textarea type="text" rows="6" id="textInput" class="form-control text-input" width="100%" name="'.to_seo_url(strip_tags(entityDecode($val['quiz_qns_question'],5))).'"></textarea>
                                                    <p class="text-end" style="font-size: 10px; margin-top: -20px; margin-right: 12px;">
                                                        Words (<span class="word-count">0</span>)
                                                    </p>
                                                </div>
                                            </div>';
                                        }
                                    }
                                    echo'
                                    </div>
                                    </div>
                                    <center><button id="endStart" class="btn btn-success w-25"><i class="fa fa-check me-1"></i>Submit Quiz</button></center>
                                    <script>
                                        $("#endStart").on("click",function(){
                                            var quizFrom = $(".quizFrom").serialize()                                                    
                                            $.ajax({
                                                 url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                                ,method     : "POST"
                                                ,data       : quizFrom
                                                ,success    : function() {
                                                    location.reload();
                                                }
                                            });
                                        });
                                    </script>';
                                }
                                echo'
                            </div>
                        </div>             
                    </div>
                </form>';
            } else {
                unset($_SESSION['QUIZ_STARTTED']);
                echo'
                <div class="lg:px-6 border-b">
                    <div class="lg:py-3 mx-auto">
                        <div class="d-flex flex-row align-items-center">
                            <h4 class="text-xl font-semibold mb-0"> 
                                '.(isset($_SESSION['QUIZ_STARTTED'])).moduleName($QUIZ['quiz_title']).'';
                                // WHEN LESSON IS COMPLETED NEXT BTN
                                if ($QUIZ['is_completed'] == 2 && $next_id != '') {
                                    echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success">Next '.moduleName($type).': '.$name.'</a>';
                                }
                                echo'
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="lg:px-6">
                    <div class="lg:py-3 mx-auto">';    
                        if (!empty($QUIZ['quiz_instruction'])) {
                            echo'
                            <h5>Instruction</h5>
                            <div>'.html_entity_decode(html_entity_decode(html_entity_decode($QUIZ['quiz_instruction']))).'</div>';
                        }
                        if (empty($QUIZ['qzstd_id'])) {
                            echo'                                    
                            <h5 class="text-danger">Notice!</h5>
                            <p class="text-danger fw-bold">Once Your Strat Your Quiz Your Only Pass/Fail It Or Cancel It Your Can Not Skip It Or Your Can Not Do Another Thing.</p>
                            <button id="quizStart" class="bg-gray-600 font-semibold hover:bg-gray-700 inline-flex items-center justify-center px-4 py-2 rounded-md text-center text-white w-full"> Start Quiz </button>
                            <script>
                                $("#quizStart").on("click",function(){
                                    $.ajax({
                                        url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                        ,method     : "POST"
                                        ,data       : {
                                                         "id_quiz"      : "'.$QUIZ['quiz_id'].'"
                                                        ,"quiz_method"  : "start"
                                        }
                                        ,success    : function(response) {
                                            window.location.href = "'.SITE_URL.'learn/'.$zone.'/quiz'.'/'.$QUIZ['quiz_id'].'";
                                        }
                                    });
                                });
                            </script>';
                        }
                        echo'
                        <div class="space-y-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" colspan="3">Quiz Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-light">
                                        <th>Question Type</th>
                                        <th class="text-center" width="150">No of Question</th>
                                        <th class="text-center" width="150">Marks</th>
                                    </tr>';
                                    if ($QUIZ['countMultipleChoice'] && $QUIZ['marksMultipleChoice']) {
                                        echo'
                                        <tr>
                                            <td>Multiple Choice</td>
                                            <td class="text-center">'.$QUIZ['countMultipleChoice'].'</td>
                                            <td class="text-center">'.$QUIZ['marksMultipleChoice'].'</td>
                                        </tr>';
                                    }
                                    if ($QUIZ['countShort'] && $QUIZ['marksShort']) {
                                        echo'
                                        <tr>
                                            <td>Short Question</td>
                                            <td class="text-center">'.$QUIZ['countShort'].'</td>
                                            <td class="text-center">'.$QUIZ['marksShort'].'</td>
                                        </tr>';
                                    }
                                    echo'
                                    <tr class="table-light">
                                        <th colspan="2">Total Marks</th>
                                        <th class="text-center">'.$QUIZ['quiz_totalmarks'].'</th>
                                    </tr>
                                    <tr class="table-light">
                                        <th colspan="2">Total Passing Marks</th>
                                        <th class="text-center">'.$QUIZ['quiz_passingmarks'].'</th>
                                    </tr>';
                                    if(isset($QUIZ['qzstd_pass_fail']) && $QUIZ['qzstd_pass_fail'] == 1){
                                        $bg_color = 'bg-success'; 
                                    } else if(isset($QUIZ['qzstd_pass_fail']) && $QUIZ['qzstd_pass_fail'] == 0){
                                        $bg_color = 'bg-danger'; 
                                    } else {
                                        $bg_color = 'bg-secondary'; 
                                    }
                                    echo'
                                    <tr class="text-white '.$bg_color.'">
                                        <th colspan="2">Obtain Marks</th>
                                        <th class="text-center">'.(!empty($QUIZ['qzstd_obtain_marks']) ? $QUIZ['qzstd_obtain_marks'] : 'Pending').'</th>
                                    </tr>';
                                    if (!empty($QUIZ['qzstd_id'])) {
                                        echo'
                                        <tr class="bg-success">
                                            <th class="text-center text-white" colspan="3">Quiz Submited</th>
                                        </tr>';
                                    }
                                    echo'
                                </tbody>
                            </tbody>
                        </div>
                    </div>
                </div>';
            }
            echo'
        </div>';
    }
    echo'
</div>';
?>