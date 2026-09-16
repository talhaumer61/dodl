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
                // Assuming $COURSES_LESSONS['lesson_video_code'] contains something like 'dQw4w9WgXcQ'
                $videoCode = $COURSES_LESSONS['lesson_video_code'];
                $isCompleted = $COURSES_LESSONS['is_completed'];
                
                echo '
                <!-- YouTube Player -->
                <div class="video-player mb-4">
                    <iframe id="youtube_player" width="100%" height="480"
                        src="https://www.youtube.com/embed/'.$videoCode.'?enablejsapi=1"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>

                <div class="lg:px-6 border-b">
                    <div class="lg:py-3 mx-auto">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-row align-items-center">
                                <h5 class="text-lg font-semibold mb-0"> 
                                    '.$COURSES_LESSONS['lesson_topic'].'
                                </h5>
                            </div>
                            <div class="text-center font-semibold mt-1">
                                <h5 id="video_remaining_time">';
                                    if ($COURSES_LESSONS['is_completed'] == 2 && $next_id != '') {
                                        echo '<a href="' . SITE_URL . 'learn/' . ZONE . '/' . $redirection . '" class="btn btn-sm btn-success px-4 py-1 rounded">Next ' . moduleName($type) . ': ' . $name . '</a>';
                                    }
                                    echo '
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>';

                // LESSON TRACKING PLAYER AND JAVASCRIPT CODE

                if ($isCompleted != 2 || $isCompleted == ''): ?>
                    <script>
                        let player, countdownInterval;
                        let remainingSeconds = 0;
                        let isPlaying = false;
                        let isTabHidden = false;

                        // Pause/resume video and timer based on tab visibility
                        document.addEventListener("visibilitychange", function () {
                            isTabHidden = document.hidden;
                            if (player && typeof player.pauseVideo === 'function') {
                                if (isTabHidden) {
                                    player.pauseVideo();
                                    stopCountdown();
                                } else {
                                    if (player.getPlayerState() === YT.PlayerState.PLAYING) {
                                        startCountdown();
                                    }
                                }
                            }
                        });

                        function initYouTubePlayer() {
                            if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
                                const tag = document.createElement('script');
                                tag.src = "https://www.youtube.com/iframe_api";
                                document.head.appendChild(tag);

                                // Set a global callback once the script loads
                                window.onYouTubeIframeAPIReady = createPlayer;
                            } else {
                                createPlayer(); // API is already loaded
                            }
                        }

                        function createPlayer() {
                            player = new YT.Player('youtube_player', {
                                events: {
                                    'onReady': onPlayerReady,
                                    'onStateChange': onPlayerStateChange
                                }
                            });
                        }


                        function onPlayerReady(event) {
                            fetch("<?= SITE_URL ?>include/ajax/get_tracking.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: new URLSearchParams({
                                    track_mood: "playing",
                                    id_video: "<?= $COURSES_LESSONS['lesson_video_code'] ?>",
                                    id_week: "<?= $COURSES_LESSONS['id_week'] ?>",
                                    id_lecture: "<?= $COURSES_LESSONS['lesson_id'] ?>",
                                    id_curs: "<?= $COURSES['curs_id'] ?>",
                                    id_mas: "<?= $COURSES['id_mas'] ?>",
                                    id_ad_prg: "<?= $COURSES['id_ad_prg'] ?>",
                                    curs_href: "<?= $COURSES['curs_href'] ?>"
                                })
                            })
                            .then(res => res.text())
                            .then(res => {
                                console.log("Tracking response:", res.trim());

                                if (res.includes("track_added") || res.includes("track_already_added")) {
                                    const waitForDuration = setInterval(() => {
                                        const duration = player.getDuration();
                                        if (duration && duration > 0) {
                                            clearInterval(waitForDuration);

                                            remainingSeconds = Math.floor(duration / 2);
                                            updateTimerDisplay(remainingSeconds);
                                            console.log("Duration fetched:", duration);
                                        }
                                    }, 300);
                                }
                            })
                            .catch(err => console.error("Tracking failed:", err));
                        }

                        function onPlayerStateChange(event) {
                            if (event.data === YT.PlayerState.PLAYING) {
                                isPlaying = true;
                                startCountdown();
                            } else if (
                                event.data === YT.PlayerState.PAUSED ||
                                event.data === YT.PlayerState.ENDED
                            ) {
                                isPlaying = false;
                                stopCountdown();
                            }
                        }

                        function startCountdown() {
                            if (countdownInterval || isTabHidden) return;

                            countdownInterval = setInterval(() => {
                                if (!isPlaying || isTabHidden) return;

                                if (remainingSeconds <= 0) {
                                    stopCountdown();
                                    completeLesson();
                                    return;
                                }

                                remainingSeconds--;
                                updateTimerDisplay(remainingSeconds);
                            }, 1000);
                        }

                        function stopCountdown() {
                            clearInterval(countdownInterval);
                            countdownInterval = null;
                        }

                        function updateTimerDisplay(seconds) {
                            const min = Math.floor(seconds / 60);
                            const sec = seconds % 60;
                            document.getElementById("video_remaining_time").textContent =
                                `${min}:${sec < 10 ? '0' + sec : sec}`;
                        }

                        function completeLesson() {
                            document.getElementById("video_remaining_time").textContent = "Completing lecture...";

                            fetch("<?= SITE_URL ?>include/ajax/get_tracking.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: new URLSearchParams({
                                    track_mood: "completed",
                                    id_video: "<?= $COURSES_LESSONS['lesson_video_code'] ?>",
                                    id_week: "<?= $COURSES_LESSONS['id_week'] ?>",
                                    id_lecture: "<?= $COURSES_LESSONS['lesson_id'] ?>",
                                    id_curs: "<?= $COURSES['curs_id'] ?>",
                                    id_mas: "<?= $COURSES['id_mas'] ?>",
                                    id_ad_prg: "<?= $COURSES['id_ad_prg'] ?>",
                                    curs_href: "<?= $COURSES['curs_href'] ?>"
                                })
                            })
                            .then(res => res.text())
                            .then(html => {
                                const container = document.getElementById("video_remaining_time");
                                container.innerHTML = html;
                            })
                            .catch(error => {
                                console.error("Completion tracking failed:", error);
                                document.getElementById("video_remaining_time").textContent = "Failed to mark complete.";
                            });
                        }

                        // 👇 Safely call the initializer
                        document.addEventListener("DOMContentLoaded", initYouTubePlayer);
                    </script>

                    <?php 
                endif;

            } else {
                // Display title and timer for reading material
                echo '
                <div class="lg:px-6 border-b">
                    <div class="lg:py-3 mx-auto">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-row align-items-center">
                                <h5 class="text-lg font-semibold mb-0"> 
                                    '.$COURSES_LESSONS['lesson_topic'].'
                                </h5>
                            </div>
                            <div class="text-center font-semibold mt-1">
                                <h5 id="video_remaining_time">';
                                    if ($COURSES_LESSONS['is_completed'] == 2 && $next_id != '') {
                                        echo '<a href="' . SITE_URL . 'learn/' . ZONE . '/' . $redirection . '" class="btn btn-sm btn-success px-4 py-1 rounded">Next ' . moduleName($type) . ': ' . $name . '</a>';
                                    }
                                    echo '
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>';
                // LESSON TRACKING FOR THE READING MATERIAL
                if ($COURSES_LESSONS['is_completed'] != 2) {
                    ?>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const countdownTime = 30;
                            let countdown = countdownTime;
                            const el = document.getElementById("video_remaining_time");
                            if (!el) return;

                            const id_week = "<?= $COURSES_LESSONS['id_week'] ?>";
                            const id_lecture = "<?= $COURSES_LESSONS['lesson_id'] ?>";
                            const id_curs = "<?= $COURSES['curs_id'] ?>";
                            const id_mas = "<?= $COURSES['id_mas'] ?>";
                            const id_ad_prg = "<?= $COURSES['id_ad_prg'] ?>";
                            const curs_href = "<?= $COURSES['curs_href'] ?>";
                            const track_url = "<?= SITE_URL ?>include/ajax/get_tracking.php";

                            const timer = setInterval(() => {
                                if (countdown > 0) {
                                    el.innerHTML = `${countdown} sec`;
                                    countdown--;
                                } else {
                                    clearInterval(timer);

                                    const formData = new URLSearchParams({
                                        id_week,
                                        id_lecture,
                                        id_curs,
                                        id_mas,
                                        id_ad_prg,
                                        curs_href,
                                        track_mood: "reading_metrail"
                                    });

                                    fetch(track_url, {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded"
                                        },
                                        body: formData.toString()
                                    })
                                    .then(res => res.text())
                                    .then(data => {
                                        if (data !== "reading_metrail_completed") {
                                            el.innerHTML = data; // replace timer with button
                                        }
                                    })
                                    .catch(err => {
                                        console.error("Reading material tracking error:", err);
                                    });
                                }
                            }, 1000);
                        });
                        </script>
                    <?php
                }
            }

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
                                echo '<div class="alert alert-danger mb-0 text-center"><strong>No data found...!</strong></div>';
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
                                                                 LEFT JOIN '.COURSES_DISCUSSIONSTUDENTS.' cds on cds.id_discussion = cd.discussion_id AND cds.id_std = '.$_SESSION['userlogininfo']['STDID'].''
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
                                include 'discussion_board/query.php';
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
                                <textarea class="form-control" rows="10" id="std_review">'.html_entity_decode($COURSES_LESSONS['std_review'] ?? '', ENT_QUOTES).'</textarea>
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
                                                ORDER BY qzstd_id DESC 
                                                LIMIT 1
                                            ) AS qs ON qs.id_quiz = q.quiz_id
                                         LEFT JOIN ' . LECTURE_TRACKING . ' lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_quiz = q.quiz_id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')',
                        'where'     => array(
                                                'q.quiz_status' => 1,
                                                'q.is_publish'  => 1,
                                                'q.is_deleted'  => 0,
                                                'q.quiz_id'     => cleanvars($slug)
                                            ),
                        'return_type' => 'single'
                    );
            $QUIZ = $dblms->getRows(QUIZ . ' q', $con);

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
                                                                <input type="radio" id="o_'.$key.'_'.$optionFlag.'" name="o_'.$key.'" value="'.($okey).'">
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
                                        $("#endStart").on("click", function () {
                                            var quizFrom = $(".quizFrom").serialize();                                                   
                                            $.ajax({
                                                url: "' . SITE_URL . 'include/ajax/get_quiz.php",
                                                method: "POST",
                                                data: quizFrom,
                                                success: function (response) {
                                                    alert("Server Response: " + response); // ✅ Show server response in console
                                                },
                                                error: function (xhr, status, error) {
                                                    console.log("An error occurred: " + error); // Optional: show error
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
                echo '
                <div class="lg:px-6 border-b border-gray-300 bg-gray-50 shadow-sm">
                    <div class="lg:py-4 mx-auto">
                        <div class="d-flex flex-row align-items-center justify-content-between flex-wrap">
                            <h4 class="text-2xl font-bold text-gray-800 mb-0"> 
                                '.moduleName($QUIZ['quiz_title']).'
                            </h4>';
                            if ($QUIZ['is_completed'] == 2 && $next_id != '') {
                                echo '
                                <a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" 
                                class="btn btn-sm btn-success px-4 py-1 rounded">
                                Next '.moduleName($type).': '.$name.'
                                </a>';
                            }
                        echo '
                        </div>
                    </div>
                </div>
                <div class="lg:px-6 mt-4">
                    <div class="lg:py-3 mx-auto bg-white shadow-md rounded-lg p-4 border border-gray-200">';
                        
                        if (!empty($QUIZ['quiz_instruction'])) {
                            echo '
                            <h5 class="text-lg fw-semibold mb-2 border-bottom pb-1 text-gray-800">📘 Instructions</h5>
                            <div class="text-gray-700 mb-4 lh-base">'.html_entity_decode(html_entity_decode(html_entity_decode($QUIZ['quiz_instruction']))).'</div>';
                        }

                        $showStartButton = false;

                        if (empty($QUIZ['qzstd_id'])) {
                            $showStartButton = true;
                        } elseif ($QUIZ['is_completed'] == 1 && !empty($QUIZ['qzstd_submited_date'])) {
                            $lastAttemptTime = strtotime($QUIZ['qzstd_submited_date']);
                            $currentTime = time();
                            if (($currentTime - $lastAttemptTime) >= 900) {
                                $showStartButton = true;
                            }
                        }

                        if ($showStartButton) {
                            echo '
                            <div class="bg-red-50 border-start border-4 border-danger p-3 rounded mb-4">
                                <h5 class="text-danger fw-semibold mb-2">⚠️ Important Notice</h5>
                                <p class="text-danger mb-0">
                                    Once you start your quiz, you can only Pass/Fail or Cancel it. 
                                    You cannot skip it or do any other activity until you finish.
                                </p>
                            </div>

                            <button id="quizStart" 
                                    class="bg-indigo-600 fw-semibold hover:bg-indigo-700 transition-all duration-200 d-flex align-items-center justify-content-center px-4 py-2 rounded text-white w-100 shadow-sm">
                                🚀 Start Quiz
                            </button>
                            <script>
                                $("#quizStart").on("click", function() {
                                    $.ajax({
                                        url: "'.SITE_URL.'include/ajax/get_quiz.php",
                                        method: "POST",
                                        data: {
                                            id_quiz: "'.$QUIZ['quiz_id'].'",
                                            quiz_method: "start"
                                        },
                                        success: function(response) {
                                            window.location.href = "'.SITE_URL.'learn/'.$zone.'/quiz/'.$QUIZ['quiz_id'].'";
                                        }
                                    });
                                });
                            </script>';
                        }

                        echo '
                        <div class="mt-4">
                            <h5 class="text-lg fw-semibold mb-3 border-bottom pb-1 text-gray-800">📊 Quiz Marks Overview</h5>
                            <div class="table-responsive shadow-sm rounded border">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="bg-gray-100 text-dark fw-semibold text-uppercase">
                                        <tr>
                                            <th>Question Type</th>
                                            <th class="text-center" width="200">No. of Questions</th>
                                            <th class="text-center" width="150">Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                                        if ($QUIZ['countMultipleChoice'] && $QUIZ['marksMultipleChoice']) {
                                            echo '
                                            <tr>
                                                <td>Multiple Choice</td>
                                                <td class="text-center">'.$QUIZ['countMultipleChoice'].'</td>
                                                <td class="text-center">'.$QUIZ['marksMultipleChoice'].'</td>
                                            </tr>';
                                        }

                                        if ($QUIZ['countShort'] && $QUIZ['marksShort']) {
                                            echo '
                                            <tr>
                                                <td>Short Questions</td>
                                                <td class="text-center">'.$QUIZ['countShort'].'</td>
                                                <td class="text-center">'.$QUIZ['marksShort'].'</td>
                                            </tr>';
                                        }

                                        echo '
                                        <tr class="bg-light fw-semibold">
                                            <td colspan="2">Total Marks</td>
                                            <td class="text-center">'.$QUIZ['quiz_totalmarks'].'</td>
                                        </tr>
                                        <tr class="bg-light fw-semibold">
                                            <td colspan="2">Passing Marks</td>
                                            <td class="text-center">'.$QUIZ['quiz_passingmarks'].'</td>
                                        </tr>';

                                        if (isset($QUIZ['qzstd_pass_fail']) && $QUIZ['qzstd_pass_fail'] == 1) {
                                            $bg_color = 'bg-success';
                                        } elseif (isset($QUIZ['qzstd_pass_fail']) && $QUIZ['qzstd_pass_fail'] == 0) {
                                            $bg_color = 'bg-danger';
                                        } else {
                                            $bg_color = 'bg-secondary';
                                        }

                                        echo '
                                        <tr class="text-white '.$bg_color.' fw-semibold">
                                            <td colspan="2">Obtained Marks</td>
                                            <td class="text-center">'.$QUIZ['qzstd_obtain_marks'].'</td>
                                        </tr>';

                                        if (!empty($QUIZ['qzstd_id'])) {
                                            echo '
                                            <tr class="bg-success text-white text-center fw-semibold">
                                                <td colspan="3">✅ Quiz Submitted</td>
                                            </tr>';
                                        }

                                    echo '
                                    </tbody>
                                </table>
                            </div>
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