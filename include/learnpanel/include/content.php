<?php
echo'
<div '.($quizLocked ? '' : 'class="main_content mb-5"').'>';
    // IF STUDENT WANT TO SEE COURSE LESSONS
    if ($view === 'lesson') {
        $con = array(
                         'select'       => 'l.lesson_id, l.id_week, l.id_lecture, l.lesson_topic, l.lesson_content, l.lesson_video_code, l.lesson_video_code_vimeo, l.lesson_detail, l.lesson_reading_detail, lt.is_completed, lt.my_note_pad, lt.std_review'
                        ,'join'         => 'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_lecture = l.lesson_id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].') AND lt.is_deleted = 0'
                        ,'where'        => array(
                                                        'l.lesson_status'    => 1
                                                    ,'l.is_deleted'       => 0
                                                    ,'l.lesson_id'        => cleanvars($slug)
                                                )
                        ,'return_type'  => 'single'
                    );
        $COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS.' AS l',$con);
        echo '
        <div class="relative z-10 bg-gray-50 dark:bg-gray-900">

            <!-- Lesson Video / Reading -->
            <div class="px-6 py-6">';
                if ($COURSES_LESSONS['lesson_content'] != 2) {
                    $videoId = $COURSES_LESSONS['lesson_video_code_vimeo']; // '1132011025', '1141042458'
                    $accessToken = VIMEO_ACCESS_TOKEN;

                    // --------------- Vimeo API Call (Corrected) ------------------
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.vimeo.com/videos/$videoId");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Authorization: Bearer $accessToken"
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($httpCode == 200) {
                        $videoInfo = json_decode($response, true);
                    } else {
                        $videoInfo = null;
                    }
                    if(isset($videoInfo['embed']['html'])){
                        echo '
                        <div class="embed-video rounded-lg shadow-lg overflow-hidden">';
                            echo $videoInfo['embed']['html'];
                            if ($COURSES_LESSONS['is_completed'] != 2) {
                                include('get_time_and_track_lecture.php');
                            }
                            echo '
                        </div>';
                    } else {                        
                        echo '
                        <div class="rounded-lg shadow-lg p-4 overflow-hidden">
                            <i class="text-danger">Loading video...</i>
                        </div>';
                    }
                } else {
                    if ($COURSES_LESSONS['is_completed'] != 2) {
                        echo '
                        <script>
                            setTimeout(function() {
                                var id_week = "'.$COURSES_LESSONS['id_week'].'";
                                var id_lecture = "'.$COURSES_LESSONS['lesson_id'].'";
                                var id_curs = "'.$COURSES['curs_id'].'";
                                var id_mas = "'.$COURSES['id_mas'].'";
                                var id_ad_prg = "'.$COURSES['id_ad_prg'].'";
                                var curs_href = "'.$COURSES['curs_href'].'";
                                var videoRemainingTime = document.getElementById("video_remaining_time");
                                $.ajax({
                                    url: "'.SITE_URL.'include/ajax/get_tracking.php",
                                    method: "POST",
                                    data: {
                                        id_week: id_week,
                                        id_lecture: id_lecture,
                                        id_curs: id_curs,
                                        id_mas: id_mas,
                                        id_ad_prg: id_ad_prg,
                                        curs_href: curs_href,
                                        track_mood: "reading_metrail"
                                    },
                                    success: function(e) {
                                        if (e != "reading_metrail_completed") {
                                            videoRemainingTime.innerHTML = e;
                                        }
                                    }
                                });
                            }, 10000);
                        </script>';
                    }
                }
                echo '
                <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg shadow p-4 border">
                    <!-- Header Section -->
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-xl mb-0 font-semibold text-gray-800 dark:text-gray-200">'.$COURSES_LESSONS['lesson_topic'].'</h4>
                        <div id="video_remaining_time" class="text-sm font-medium text-indigo-600">';
                            if ($COURSES_LESSONS['is_completed'] == 2 && $next_id != '') {
                                echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success px-4 py-1 rounded"> Next '.moduleName($type).': '.html_entity_decode($name).'</a>';
                            }
                            echo '
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-3 border-gray-300 dark:border-gray-700">

                    <!-- Lesson Description -->
                    <div class="text-gray-700 dark:text-gray-300">';
                        if(!empty($COURSES_LESSONS['lesson_reading_detail'])){
                            echo'
                            <div class="clamp-text" data-lines="5">
                                '.html_entity_decode(html_entity_decode($COURSES_LESSONS['lesson_reading_detail'])).'
                            </div>';
                        } else {
                            echo '<p class="text-gray-400 dark:text-gray-500 italic">No reading details available for this lesson.</p>';
                        }
                        echo '
                    </div>
                </div>
            </div>

            <!-- Lesson Tabs -->
            <div class="px-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border">
                    <nav class="cd-secondary-nav border-bottom bg-white shadow-sm rounded-top">
                        <ul uk-switcher="connect: #lesson-tabs; animation: uk-animation-fade" class="flex flex-wrap justify-start text-sm font-medium text-gray-700 dark:text-gray-300">
                            <li class="nav-item"><a class="active" href="#"><i class="fa fa-book me-1"></i>Topic Description</a></li>
                            <li class="nav-item"><a href="#"><i class="fa fa-folder-open me-1"></i>Topic Resources</a></li>
                            <li class="nav-item"><a href="#"><i class="fa fa-comments me-1"></i>Discussion Board</a></li>
                            <li class="nav-item"><a href="#"><i class="fa fa-pencil-alt me-1"></i>Note Book</a></li>
                            <li class="nav-item"><a href="#"><i class="fa fa-bullhorn me-1"></i>Announcements</a></li>
                            <li class="nav-item"><a href="#"><i class="fa fa-question-circle me-1"></i>Q&A</a></li>
                            <li class="nav-item"><a href="#"><i class="fa fa-star me-1"></i>Review</a></li>
                        </ul>
                    </nav>
                    <hr class="my-3 border-gray-300 dark:border-gray-700">
                    <div id="lesson-tabs" class="uk-switcher">

                        <!-- Topic Description -->
                        <div class="mt-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card shadow-sm border-0 rounded-3">
                                        <div class="card-header alert-primary d-flex align-items-center">
                                            <i class="fa fa-book me-1" style="font-size:14px;"></i>
                                            <b class="mb-0">Topic Description</b>
                                        </div>
                                        <div class="card-body bg-light">';
                                            if (!empty($COURSES_LESSONS['lesson_detail'])) {
                                                echo '
                                                <div class="text-gray-800 leading-relaxed" style="line-height:1.7; font-size:15px;">
                                                    <div class="clamp-text" data-lines="5">
                                                        '.html_entity_decode(html_entity_decode($COURSES_LESSONS['lesson_detail'])).'
                                                    </div>
                                                </div>';
                                            } else {
                                                echo '
                                                <div class="alert alert-danger text-center py-3 mb-0 rounded-2 shadow-sm">
                                                    <i class="	fa fa-warning me-2 fs-5"></i>
                                                    No description found for this lesson.
                                                </div>';
                                            }
                                            echo '
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Topic Resources -->
                        <div class="mt-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card shadow-sm border-0 rounded-3">
                                        <div class="card-header alert-primary d-flex align-items-center">
                                            <i class="fa fa-folder-open me-1" style="font-size:14px;"></i>
                                            <b class="mb-0">Topic Resources</b>
                                        </div>
                                        <div class="card-body bg-light">
                                            <div class="row g-3">';
                                                $con = array(
                                                    'select'       => 'cd.id, cd.file_name, cd.url, cd.file',
                                                    'where'        => array(
                                                        'cd.id_curs'   => cleanvars($COURSES['curs_id']),
                                                        'cd.id_lesson' => cleanvars($COURSES_LESSONS['lesson_id']),
                                                        'cd.status'    => 1,
                                                        'cd.is_deleted'=> 0    
                                                    ),
                                                    'order_by'     => 'cd.id ASC',
                                                    'return_type'  => 'all'
                                                );
                                                $COURSES_DOWNLOADS = $dblms->getRows(COURSES_DOWNLOADS.' AS cd', $con, $sql);

                                                if($COURSES_DOWNLOADS){
                                                    foreach ($COURSES_DOWNLOADS as $CdVal) {
                                                        echo '
                                                        <div class="col-md-4">
                                                            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white hover-shadow-sm">
                                                                <div class="mb-2">
                                                                    <h6 class="fw-semibold text-dark mb-1">
                                                                        <i class="fa fa-file-alt me-1"></i>'.moduleName($CdVal['file_name']).'
                                                                    </h6>
                                                                </div>
                                                                <div class="d-flex gap-2">';
                                                                    if(!empty($CdVal['url'])){
                                                                        echo '
                                                                        <a href="'.$CdVal['url'].'" target="_blank" class="btn btn-sm btn-primary flex-grow-1">
                                                                            <i class="fa fa-link me-1"></i>Open URL
                                                                        </a>';
                                                                    }
                                                                    if(!empty($CdVal['file'])){
                                                                        echo '
                                                                        <a href="'.SITE_URL_PORTAL.'uploads/files/lesson_plan/'.$CdVal['file'].'" target="_blank" class="btn btn-sm btn-success flex-grow-1">
                                                                            <i class="fa fa-download me-1"></i>Download
                                                                        </a>';
                                                                    }
                                                                echo '
                                                                </div>
                                                            </div>
                                                        </div>';
                                                    }
                                                } else {
                                                    echo '
                                                    <div class="col">
                                                        <div class="alert alert-danger text-center py-3 mb-0 rounded-2 shadow-sm">
                                                            <i class="ri-error-warning-line me-2 fs-5"></i>
                                                            No resources found for this lesson.
                                                        </div>
                                                    </div>';
                                                }
                                                echo '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discussion Board -->
                        <div class="mt-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card shadow-sm border-0 rounded-3">
                                        <div class="card-header alert-primary d-flex align-items-center">
                                            <i class="fa fa-comments me-1" style="font-size:14px;"></i>
                                            <b class="mb-0">Discussion Board</b>
                                        </div>
                                        <div class="card-body bg-light">';
                                            $con = array(
                                                'select'       => 'cd.discussion_id, cd.discussion_subject, cd.discussion_detail, cd.id_lecture, cds.dst_detail, a.adm_photo, a.adm_fullname, e.emply_gender',
                                                'join'         => 'INNER JOIN '.ADMINS.' a ON a.adm_id = cd.id_added
                                                                LEFT JOIN '.EMPLOYEES.' e ON e.emply_id = cd.id_teacher
                                                                LEFT JOIN '.COURSES_DISCUSSIONSTUDENTS.' cds ON cds.id_discussion = cd.discussion_id AND cds.id_std = '.$_SESSION['userlogininfo']['STDID'],
                                                'where'        => array(
                                                    'cd.id_curs'           => cleanvars($COURSES['curs_id']),
                                                    'cd.discussion_status' => 1,
                                                    'cd.is_deleted'        => 0
                                                ),
                                                'search_by'    => ' AND FIND_IN_SET('.$COURSES_LESSONS['id_lecture'].',cd.id_lecture)',
                                                'return_type'  => 'all'
                                            );
                                            $COURSES_DISCUSSION = $dblms->getRows(COURSES_DISCUSSION.' cd', $con, $sql);

                                            if($COURSES_DISCUSSION){
                                                include 'discussion_board/query.php';
                                                foreach ($COURSES_DISCUSSION as $CdVal) {

                                                    // Default avatar
                                                    $photo = ($CdVal['emply_gender'] == '2') 
                                                            ? SITE_URL_PORTAL.'uploads/images/default_female.jpg' 
                                                            : SITE_URL_PORTAL.'uploads/images/default_male.jpg';

                                                    // Admin photo
                                                    if(!empty($CdVal['adm_photo'])){
                                                        $file_url = SITE_URL_PORTAL.'uploads/images/admin/'.$CdVal['adm_photo'];
                                                        if (check_file_exists($file_url)) {
                                                            $photo = $file_url;
                                                        }
                                                    }

                                                    echo '
                                                    <div class="row g-3 mb-3 align-items-start">
                                                        <div class="col-auto">
                                                            <img src="'.$photo.'" class="rounded-circle border border-primary" style="width:50px; height:50px; object-fit:cover;">
                                                        </div>
                                                        <div class="col">
                                                            <h6 class="fw-semibold mb-1"><i class="fa fa-user me-1"></i>'.$CdVal['adm_fullname'].'</h6>
                                                            <p class="text-dark fw-semibold mb-1"><i class="fa fa-comment-alt me-1"></i>'.moduleName($CdVal['discussion_subject']).'</p>
                                                            <p class="text-gray-700">'.html_entity_decode(html_entity_decode($CdVal['discussion_detail'])).'</p>
                                                        </div>
                                                        <div class="col-auto text-end">';
                                                            if(empty($CdVal['dst_detail'])){
                                                                echo '<button class="btn btn-sm btn-dark" onclick="show_modal(\''.SITE_URL.'include/modals/learn/discussion_board/add.php?id_discussion='.cleanvars($CdVal['discussion_id']).'\');">
                                                                        <i class="fa fa-plus-circle me-1"></i>Submit Your Discussion
                                                                    </button>';
                                                            } else {
                                                                echo '<span class="btn btn-sm btn-success"><i class="fa fa-check-circle me-1"></i>Submitted</span>';
                                                            }
                                                            echo '
                                                        </div>
                                                    </div>';
                                                }
                                            } else {
                                                echo '
                                                <div class="alert alert-danger text-center py-3 mb-0 rounded-2 shadow-sm">
                                                    <i class="fa fa-warning me-2 fs-5"></i>
                                                    No discussions found for this lesson.
                                                </div>';
                                            }
                                            echo '
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Note Book -->
                        <div class="mt-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card shadow-sm border-0 rounded-3">
                                        <div class="card-header alert-primary d-flex align-items-center">
                                            <i class="fa fa-sticky-note me-1" style="font-size:14px;"></i>
                                            <b class="mb-0">Make Your Own Notes</b>
                                        </div>
                                        <div class="card-body bg-light">
                                            <div class="form-group">
                                                <textarea class="form-control rounded-2" rows="10" id="myNotePad" placeholder="Write your notes here...">'.html_entity_decode(html_entity_decode($COURSES_LESSONS['my_note_pad'])).'</textarea>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    $("#myNotePad").on("input", function() {
                                                        var myNote = $(this).val().trim();
                                                        if (myNote !== "") {
                                                            var id_week      = "'.$COURSES_LESSONS['id_week'].'";
                                                            var id_lecture   = "'.$COURSES_LESSONS['lesson_id'].'";
                                                            var id_curs      = "'.$COURSES['curs_id'].'";
                                                            var curs_href    = "'.$COURSES['curs_href'].'";
                                                            var id_mas       = "'.$COURSES['id_mas'].'";
                                                            var id_ad_prg    = "'.$COURSES['id_ad_prg'].'";
                                                            var id_std       = "'.$_SESSION['userlogininfo']['STDID'].'";

                                                            $.ajax({
                                                                url    : "'.SITE_URL.'include/ajax/get_tracking.php",
                                                                method : "POST",
                                                                data   : {
                                                                    "id_week"      : id_week,
                                                                    "id_lecture"   : id_lecture,
                                                                    "id_curs"      : id_curs,
                                                                    "curs_href"    : curs_href,
                                                                    "id_mas"       : id_mas,
                                                                    "id_ad_prg"    : id_ad_prg,
                                                                    "myNote"       : myNote,
                                                                    "id_std"       : id_std,
                                                                    "track_mood"   : "my_note_pad_saved"
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
                        </div>

                        <!-- Announcements -->
                        <div class="mt-4">
                            <div class="row g-3">';
                                // Fetch Announcements
                                $con = array(
                                    'select'      => 'ca.announcement_topic, ca.announcement_detail, ca.id_lecture, ca.date_added, a.adm_photo, a.adm_fullname, e.emply_gender',
                                    'join'        => 'INNER JOIN '.ADMINS.' a on a.adm_id = ca.id_added
                                                    LEFT JOIN '.EMPLOYEES.' e on e.emply_id = ca.id_teacher',
                                    'where'       => array(
                                                        'ca.id_curs'              => cleanvars($COURSES['curs_id']),
                                                        'ca.announcement_status'  => 1,
                                                        'ca.is_deleted'           => 0
                                                    ),
                                    'return_type' => 'all'
                                );
                                $COURSES_ANNOUNCEMENTS = $dblms->getRows(COURSES_ANNOUNCEMENTS.' ca', $con);

                                if($COURSES_ANNOUNCEMENTS){
                                    foreach($COURSES_ANNOUNCEMENTS as $CaVal){
                                        // Default avatar
                                        $photo = ($CaVal['emply_gender'] == '2') 
                                                ? SITE_URL_PORTAL.'uploads/images/default_female.jpg' 
                                                : SITE_URL_PORTAL.'uploads/images/default_male.jpg';

                                        // Admin photo
                                        if(!empty($CaVal['adm_photo'])){
                                            $file_url = SITE_URL_PORTAL.'uploads/images/admin/'.$CaVal['adm_photo'];
                                            if (check_file_exists($file_url)) {
                                                $photo = $file_url;
                                            }
                                        }

                                        echo '
                                        <div class="col-12">
                                            <div class="card shadow-sm border-0 rounded-3">
                                                <div class="card-header alert-primary d-flex align-items-center">
                                                    <i class="fa fa-bullhorn me-1" style="font-size:14px;"></i>
                                                    <b class="mb-0">'.htmlspecialchars($CaVal['announcement_topic']).'</b>
                                                    <span class="ms-auto text-muted small">Date: '.date("d M, Y", strtotime($CaVal['date_added'])).'</span>
                                                </div>
                                                <div class="card-body bg-light d-flex align-items-start">
                                                    <img src="'.$photo.'" alt="avatar" class="rounded-circle me-3" style="width:45px; height:45px; object-fit:cover;">
                                                    <div>
                                                        <h6 class="fw-semibold mb-1">'.$CaVal['adm_fullname'].'</h6>
                                                        <p class="mb-0 text-gray-700">'.html_entity_decode(html_entity_decode($CaVal['announcement_detail'])).'</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                                    }
                                } else {
                                    echo '<div class="col-12">
                                            <div class="alert alert-danger text-center mb-0 py-3 rounded-2 shadow-sm">
                                                <i class="fa fa-exclamation-triangle me-2"></i>
                                                No announcements found for this lesson.
                                            </div>
                                        </div>';
                                }
                                echo'
                            </div>
                        </div>

                        <!-- Q&A -->
                        <div class="mt-4">
                            <div class="row">                        
                                <div class="col">
                                    <div class="card border-0">
                                        <div class="card-header alert-primary">
                                            <i class="fa fa-question-circle me-1" style="font-size:14px;"></i>
                                            <b class="mb-0">Ask question to teacher</b>
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
                        <div class="mt-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card shadow-sm border-0 rounded-3">
                                        <div class="card-header alert-primary d-flex align-items-center">
                                            <i class="fa fa-star me-1" style="font-size:14px;"></i>
                                            <b class="mb-0">Lecture Review </b> <span class="text-danger"> (Once you write your review it will be saved automatically)</span>
                                        </div>
                                        <div class="card-body bg-light">
                                            <div class="form-group">
                                                <textarea class="form-control rounded-2" rows="10" id="std_review" placeholder="Review about the Lecture Topic...">'.html_entity_decode($COURSES_LESSONS['std_review'] ?? '', ENT_QUOTES).'</textarea>
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
                        </div>

                    </div>
                </div>
            </div>
        </div>';
    }

    // IF STUDENT WANT TO SEE COURSE ASSIGNMENTS
    else if ($view === 'assignments') {
        //COURSES ASSIGNMENTS
        $con = array(
                         'select'       => 'ca.id,ca.caption,ca.detail,ca.fileattach,ca.date_start,ca.date_end,e.emply_name,cas.student_file,ca.id_week,ca.id_curs,lt.is_completed'
                        ,'join'         => 'LEFT JOIN '.EMPLOYEES.' e ON e.emply_id = ca.id_teacher 
                                            LEFT JOIN '.COURSES_ASSIGNMENTS_STUDENTS.' cas ON ca.id = cas.id_assignment AND cas.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND cas.id_curs = '.$COURSES['curs_id'].' AND cas.id_mas = '.$COURSES['id_mas'].' AND cas.id_ad_prg = '.$COURSES['id_ad_prg'].'
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
        echo '
        <div class="relative z-10 px-6 py-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border p-6">

                <!-- Assignment Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-2 md:mb-0">
                        '.html_entity_decode($COURSES_ASSIGNMENTS['caption']).'
                    </h4>';
                    if ($COURSES_ASSIGNMENTS['is_completed'] == 2 && $next_id != '') {
                        echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success px-4 py-1 rounded">Next '.moduleName($type).': '.$name.'</a>';
                    }
                    echo '
                </div>

                <!-- Divider -->
                <hr class="border-gray-300 dark:border-gray-700 mb-4">

                <!-- Assignment Description -->
                <div class="text-gray-700 dark:text-gray-300 mb-6">
                    '.html_entity_decode(html_entity_decode($COURSES_ASSIGNMENTS['detail'])).'
                </div>

                <!-- File Preview Section -->
                <div class="mb-6">';
                    $extension = pathinfo($COURSES_ASSIGNMENTS['fileattach'], PATHINFO_EXTENSION);
                    $officeFiles = ['xlsx','xls','doc','docx','ppt','pptx'];
                    $pdfFiles = ['pdf'];
                    $imgFiles = ['png','jpg','jpeg'];

                    if (!empty($COURSES_ASSIGNMENTS['fileattach']) && $COURSES_ASSIGNMENTS['is_completed'] != 2) {
                        // Wrapper with fixed height 50% viewport
                        echo '
                        <div class="embed-video mb-4 rounded overflow-hidden shadow-sm border h-[50vh] w-full">';
                            if (in_array($extension, $officeFiles)) {
                                echo '<iframe class="w-full h-full" src="https://view.officeapps.live.com/op/embed.aspx?src='.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>';
                            } elseif (in_array($extension, $pdfFiles)) {
                                echo '<iframe class="w-full h-full" src="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>';
                            } elseif (in_array($extension, $imgFiles)) {
                                echo '<img src="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'" alt="'.$COURSES_ASSIGNMENTS['caption'].'" class="w-full h-full object-contain">';
                            }
                            echo '
                        </div>';
                    }
                    echo '
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                    <!-- Assignment Download -->
                    '.(!empty($COURSES_ASSIGNMENTS['fileattach']) ? '<a href="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'" download target="_blank" class="btn btn-sm btn-primary px-4 py-2 rounded-md text-white flex items-center gap-1">
                        <i class="fa fa-download"></i> Assignment File
                    </a>' : '').'

                    <!-- Student Uploaded File -->
                    '.(!empty($COURSES_ASSIGNMENTS['student_file']) ? '<a href="'.SITE_URL.'uploads/files/student_assignments/'.$COURSES_ASSIGNMENTS['student_file'].'" download target="_blank" class="btn btn-sm btn-dark px-4 py-2 rounded-md text-white flex items-center gap-1">
                        <i class="fa fa-download"></i> Your File
                    </a>' : '<a class="btn btn-sm btn-success px-4 py-2 rounded-md text-white flex items-center gap-1" onclick="show_modal(\''.SITE_URL.'include/modals/learn/assignments/add.php?id_week='.cleanvars($COURSES_ASSIGNMENTS['id_week']).'&id_curs='.cleanvars($COURSES['curs_id']).'&id_mas='.cleanvars($COURSES['id_mas']).'&id_ad_prg='.cleanvars($COURSES['id_ad_prg']).'&id_assignment='.cleanvars($slug).'&id_lecture='.cleanvars($COURSES_ASSIGNMENTS['id']).'&caption='.$COURSES_ASSIGNMENTS['caption'].'\');">
                        <i class="fa fa-upload"></i> Submit Assignment
                    </a>').'

                    <!-- Next Button (if completed) -->
                    '.($COURSES_ASSIGNMENTS['is_completed'] == 2 && $next_id != '' ? '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success px-4 py-2 rounded-md text-white flex items-center gap-1">
                        Next '.moduleName($type).': '.$name.'
                    </a>' : '').'
                </div>

            </div>
        </div>';
    }

    // IF STUDENT WANT TO SEE COURSE QUIZ
    else if ($view === 'quiz') {
        echo'
        <div class="relative z-10">';
            //COURSES QUIZ
            $con = array(
                        'select'    => 'q.quiz_id, q.quiz_title, q.quiz_instruction, q.quiz_time, q.quiz_totalmarks, q.quiz_passingmarks, q.quiz_no_qns, q.id_week, q.id_curs, qs.qzstd_id, qs.qzstd_submited_date, lt.is_completed, lt.quiz_attempts,
                                        COUNT(CASE WHEN qq.quiz_qns_type = 3 THEN qq.quiz_qns_id ELSE NULL END) AS countMultipleChoice,
                                        COUNT(CASE WHEN qq.quiz_qns_type = 1 THEN qq.quiz_qns_id ELSE NULL END) AS countShort,
                                        SUM(CASE WHEN qq.quiz_qns_type = 3 THEN qq.quiz_qns_marks ELSE NULL END) AS marksMultipleChoice,
                                        SUM(CASE WHEN qq.quiz_qns_type = 1 THEN qq.quiz_qns_marks ELSE NULL END) AS marksShort,
                                        qs.qzstd_obtain_marks,
                                        qs.qzstd_pass_fail',
                        'join'      =>  'INNER JOIN ' . QUIZ_QUESTIONS . ' qq ON qq.id_quiz = q.quiz_id
                                         LEFT JOIN (
                                                SELECT * 
                                                FROM ' . QUIZ_STUDENTS . ' 
                                                WHERE id_std = ' . cleanvars($_SESSION['userlogininfo']['STDID']) . '
                                                AND id_quiz = ' . cleanvars($slug) . '
                                                AND is_deleted = 0
                                                ORDER BY qzstd_id DESC 
                                                LIMIT 1
                                            ) AS qs ON qs.id_quiz = q.quiz_id
                                         LEFT JOIN ' . LECTURE_TRACKING . ' lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_quiz = q.quiz_id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].') AND lt.is_deleted = 0',
                        'where'     => array(
                                                'q.quiz_status' => 1,
                                                'q.is_publish'  => 1,
                                                'q.is_deleted'  => 0,
                                                'q.quiz_id'     => cleanvars($slug)
                                            ),
                        'return_type' => 'single'
                    );
            $QUIZ = $dblms->getRows(QUIZ . ' q', $con, $sql);
            
            if (isset($_SESSION['QUIZ_STARTTED'])) {
                $idx = $_SESSION['CURRENT_Q_IDX'];
                $current_q = $_SESSION['QUIZ_STARTTED'][$idx];
                
                // --- NEW DYNAMIC TIME CALCULATION ---
                // Rule: 1 Mark = 1 Minute (60 seconds)
                // If quiz_qns_marks is 1, time is 60s. If 2, time is 120s.
                $marks = (!empty($current_q['quiz_qns_marks'])) ? $current_q['quiz_qns_marks'] : 1;
                $q_time = $marks * 60; 
                // ------------------------------------
                
                echo '
                <style>
                    .quiz-card { border: 1px solid #e3e6f0; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
                    .option-item { transition: all 0.2s ease; border: 1px solid #edeff5; border-radius: 8px; cursor: pointer; display: flex; align-items: center; padding: 12px 15px; margin-bottom: 10px; }
                    .option-item:hover { background-color: #f8f9fc; border-color: #4e73df; }
                    .option-item input[type="radio"] { margin-right: 12px; width: 18px; height: 18px; cursor: pointer; flex-shrink: 0; }
                    .option-label { cursor: pointer; margin-bottom: 0; flex-grow: 1; line-height: 1.4; display: flex; align-items: baseline; }
                    .timer-badge { background: #fff1f1; color: #e74a3b; padding: 5px 15px; border-radius: 20px; border: 1px solid #ffdada; font-size: 1.1rem; }
                    .marks-badge { background: #f0f4ff; color: #4e73df; padding: 2px 10px; border-radius: 5px; font-size: 0.85rem; font-weight: bold; }
                </style>

                <div class="row justify-content-center">
                    <div class="col-lg-9 col-md-11 my-5">
                        <div class="quiz-card">
                            <div class="card-header bg-light py-3 d-flex align-items-center justify-content-between border-bottom">
                                <div>
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        Question '.($idx + 1).' of '.count($_SESSION['QUIZ_STARTTED']).' 
                                        <span class="marks-badge ms-2">'.$marks.' Marks</span>
                                    </h6>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="timer-badge fw-bold me-3">
                                        <i class="fa fa-clock me-1"></i><span id="q_timer">'.$q_time.'</span>s
                                    </div>
                                    <button type="button" onclick="cancelQuiz('.$QUIZ['quiz_id'].')" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-times me-1"></i> Cancel
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <form id="qForm">
                                    <div class="mb-4">
                                        <div class="text-dark fs-5 fw-bold mb-4" style="line-height:1.6;">
                                            '.entityDecode($current_q['quiz_qns_question'], 5).'
                                        </div>';
                                        
                                        if ($current_q['quiz_qns_type'] == 3) {
                                            $options = json_decode(html_entity_decode($current_q['quiz_qns_option']), true);
                                            echo '<div class="row">';
                                            foreach ($options as $okey => $oval) {
                                                if(empty(trim($oval['qns_option']))) continue;

                                                $letter = ($okey == 0 ? 'A' : ($okey == 1 ? 'B' : ($okey == 2 ? 'C' : 'D')));
                                                echo '
                                                <div class="col-12">
                                                    <label class="option-item" for="opt_'.$okey.'">
                                                        <input type="radio" name="answer" id="opt_'.$okey.'" value="'.$okey.'">
                                                        <span class="option-label">
                                                            <strong class="me-2 text-primary">'.$letter.')</strong> '.$oval['qns_option'].'
                                                        </span>
                                                    </label>
                                                </div>';
                                            }
                                            echo '</div>';
                                        } 
                                        else {
                                            echo '
                                            <div class="form-group">
                                                <label class="mb-2 text-muted small fw-bold">YOUR ANSWER:</label>
                                                <textarea name="answer" class="form-control shadow-sm" rows="6" placeholder="Write your detailed answer here..."></textarea>
                                            </div>';
                                        }

                                    echo '
                                    </div>

                                    <input type="hidden" name="id_quiz" value="'.$QUIZ['quiz_id'].'">
                                    <input type="hidden" name="quiz_method" value="save_question">

                                    <div class="text-end mt-4">
                                        <button type="button" onclick="submitQuestion()" class="btn btn-primary btn-lg px-5 shadow">
                                            '.($idx == count($_SESSION['QUIZ_STARTTED']) - 1 ? 'Final Submit <i class="fa fa-check-circle ms-2"></i>' : 'Next Question <i class="fa fa-arrow-right ms-2"></i>').'
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                var timeLeft = '.$q_time.';
                var timerElement = document.getElementById("q_timer");

                var countdown = setInterval(function() {
                    timeLeft--;
                    timerElement.innerText = timeLeft;
                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        submitQuestion(); 
                    }
                }, 1000);

                function submitQuestion() {
                    clearInterval(countdown);
                    var formData = $("#qForm").serialize();
                    $(".btn-primary").html(\'<i class="fa fa-spinner fa-spin"></i> Saving...\').addClass("disabled");

                    $.ajax({
                        url: "'.SITE_URL.'include/ajax/get_quiz.php",
                        type: "POST",
                        data: formData,
                        success: function(response) {
                            if(response.trim() == "next" || response.trim() == "finished") {
                                location.reload(); 
                            } else {
                                alert("Error: Could not save your answer.");
                                location.reload();
                            }
                        }
                    });
                }

                function cancelQuiz(id) {
                    if(confirm("Warning: If you cancel now, your progress for this attempt will be lost. Continue?")) {
                        $.ajax({
                            url: "'.SITE_URL.'include/ajax/get_quiz.php",
                            type: "POST",
                            data: { "id_quiz": id, "quiz_method": "cancel" },
                            success: function(response) {
                                location.reload();
                            }
                        });
                    }
                }
                </script>';
            } else {
                unset($_SESSION['QUIZ_STARTTED']);
                
                // UI Logic: Determine Status Badge & Colors
                $status_html = '<span class="badge bg-info px-3 py-2 rounded-pill"><i class="fa fa-check-circle me-1"></i> Unattempted</span>';
                $card_border = 'border-gray-200';
                if (isset($QUIZ['qzstd_pass_fail'])) {
                    if ($QUIZ['qzstd_pass_fail'] == 1) {
                        $status_html = '<span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa fa-check-circle me-1"></i> Passed</span>';
                        $card_border = 'border-success';
                    } else {
                        $status_html = '<span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fa fa-times-circle me-1"></i> Failed</span>';
                        $card_border = 'border-danger';
                    }
                }

                echo '
                <div class="bg-white border-bottom shadow-sm">
                    <div class="container-fluid py-4 px-lg-5">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-1">
                                        <li class="breadcrumb-item small text-muted text-uppercase fw-bold">Assessment</li>
                                    </ol>
                                </nav>
                                <h3 class="fw-bold text-dark mb-0">'.moduleName($QUIZ['quiz_title']).'</h3>
                            </div>
                            '.(($QUIZ['is_completed'] == 1 && $next_id != '') ? '
                            <a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-primary rounded-pill px-4 shadow-sm transition">
                                Next: '.moduleName($type).': '.$name.' <i class="fa fa-arrow-right ms-2"></i>
                            </a>' : '').'
                        </div>
                    </div>
                </div>

                <div class="container-fluid py-5 px-lg-5 bg-light">
                    <div class="row g-4">
                        
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body p-4">
                                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                        <span class="bg-indigo-100 text-indigo p-2 rounded-3 me-3"><i class="fa fa-book-open"></i></span>
                                        Quiz Instructions
                                    </h5>
                                    <div class="text-muted mb-5 fs-6 lh-lg">
                                        '.(!empty($QUIZ['quiz_instruction']) ? html_entity_decode(html_entity_decode($QUIZ['quiz_instruction'])) : 'Please read the questions carefully. Each question is timed based on its marks.').'
                                    </div>';

                                    $showStartButton = false;
                                    if (empty($QUIZ['qzstd_id'])) {
                                        $showStartButton = true;
                                    } elseif (!empty($QUIZ['qzstd_submited_date'])) {
                                        $lastAttemptTime = strtotime($QUIZ['qzstd_submited_date']);
                                        if ((time() - $lastAttemptTime) >= 1) { $showStartButton = true; }
                                    }

                                    if ($showStartButton) {
                                        echo '
                                        <div class="alert bg-warning-soft border-0 rounded-4 p-4 mb-4">
                                            <div class="d-flex">
                                                <div class="me-3 fs-3 text-warning"><i class="fa fa-exclamation-triangle"></i></div>
                                                <div>
                                                    <h6 class="fw-bold text-dark">Before you begin</h6>
                                                    <p class="small mb-0 text-muted">You cannot browse other lessons once the quiz starts. Ensure you have a stable internet connection.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <button id="quizStart" class="btn btn-indigo btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm hover-lift">
                                            <i class="fa-solid fa-hourglass-start me-2"></i> START ASSESSMENT
                                        </button>';
                                    } else {
                                        echo '
                                        <div class="text-center py-4">
                                            <div class="mb-3 fs-1 text-muted"><i class="fa fa-lock"></i></div>
                                            <h6 class="fw-bold">Retake Locked</h6>
                                            <p class="text-muted small">You can retake this quiz 15 minutes after your last attempt.</p>
                                        </div>';
                                    }
                                echo '
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold text-dark mb-0">Performance</h5>
                                        '.$status_html.'
                                    </div>';

                                    // Calculate percentage for progress bar
                                    $perc = ($QUIZ['quiz_totalmarks'] > 0) ? ($QUIZ['qzstd_obtain_marks'] / $QUIZ['quiz_totalmarks']) * 100 : 0;
                                    $bar_color = ($perc >= 50) ? 'bg-success' : 'bg-danger';

                                    echo '
                                    <div class="text-center mb-4">
                                        <div class="display-5 fw-bold text-dark">'.($QUIZ['qzstd_obtain_marks'] ?? 0).'<span class="fs-4 text-muted"> / '.$QUIZ['quiz_totalmarks'].'</span></div>
                                        <p class="text-muted small">Obtained Marks</p>
                                        <div class="progress rounded-pill shadow-sm mt-3" style="height: 10px;">
                                            <div class="progress-bar '.$bar_color.' progress-bar-striped progress-bar-animated" style="width: '.$perc.'%"></div>
                                        </div>
                                    </div>

                                    <div class="list-group list-group-flush border-top pt-3">
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light p-2 rounded-3 me-3"><i class="fa fa-list-ul text-primary"></i></div>
                                                <div>
                                                    <div class="fw-bold small">MCQs</div>
                                                    <div class="text-muted extra-small">'.$QUIZ['countMultipleChoice'].' Questions</div>
                                                </div>
                                            </div>
                                            <span class="fw-bold">'.$QUIZ['marksMultipleChoice'].'  '.($QUIZ['marksMultipleChoice'] ?? 0 > 1 ? 'Marks' : 'Mark').'</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light p-2 rounded-3 me-3"><i class="fa fa-pen-fancy text-primary"></i></div>
                                                <div>
                                                    <div class="fw-bold small">Short Questions</div>
                                                    <div class="text-muted extra-small">'.$QUIZ['countShort'].' Questions</div>
                                                </div>
                                            </div>
                                            <span class="fw-bold">'.($QUIZ['marksShort'] ?? 0).' '.($QUIZ['marksShort'] ?? 0 > 1 ? 'Marks' : 'Mark').'</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3 mt-2 bg-light rounded-3 px-3">
                                            <span class="fw-bold small text-muted">Required to Pass</span>
                                            <span class="badge bg-dark rounded-pill">'.($QUIZ['quiz_passingmarks'] ?? 0).' '.($QUIZ['quiz_passingmarks'] ?? 0 > 1 ? 'Marks' : 'Mark').'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <style>
                    .bg-indigo-100 { background-color: #e0e7ff; }
                    .text-indigo { color: #4f46e5; }
                    .btn-indigo { background-color: #4f46e5; color: white; border: none; }
                    .btn-indigo:hover { background-color: #4338ca; color: white; }
                    .bg-success-soft { background-color: #d1fae5; }
                    .bg-danger-soft { background-color: #fee2e2; }
                    .bg-warning-soft { background-color: #fffbeb; }
                    .extra-small { font-size: 0.75rem; }
                    .hover-lift:hover { transform: translateY(-3px); transition: all 0.3s ease; }
                </style>

                <script>
                    $("#quizStart").on("click", function() {
                        $(this).html(\'<i class="fa fa-spinner fa-spin me-2"></i> Initializing...\').addClass("disabled");
                        $.ajax({
                            url: "'.SITE_URL.'include/ajax/get_quiz.php",
                            method: "POST",
                            data: {
                                id_quiz: "'.$QUIZ['quiz_id'].'",
                                id_curs: "'.$QUIZ['id_curs'].'",
                                id_week: "'.$QUIZ['id_week'].'",
                                quiz_method: "start"
                            },
                            success: function(response) {
                                window.location.href = "'.SITE_URL.'learn/'.$zone.'/quiz/'.$QUIZ['quiz_id'].'";
                            }
                        });
                    });
                </script>';
            }
            echo'
        </div>';
    }
    echo'
</div>';
?>