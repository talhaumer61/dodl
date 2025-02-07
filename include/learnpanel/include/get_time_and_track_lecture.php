<?php
$originalTime = seconds_to_time($videoInfo['duration']);
echo'
<script>
  var iframe = document.querySelector("iframe");
  var videoPlayer = new Vimeo.Player(iframe);
  var totalTime = 0; // Placeholder for total duration
  var countdownInterval; // Store the countdown interval so it can be paused
  var timeRemaining; // Store the remaining time
  var isPlaying = false; // Keep track of play/pause state

  // Variables for tracking
  var id_video        = "'.$COURSES_LESSONS['lesson_video_code'].'";
  var id_week         = "'.$COURSES_LESSONS['id_week'].'";
  var id_lecture      = "'.$COURSES_LESSONS['lesson_id'].'";
  var id_curs         = "'.$COURSES['curs_id'].'";
  var id_mas          = "'.$COURSES['id_mas'].'";
  var id_ad_prg       = "'.$COURSES['id_ad_prg'].'";
  var curs_href       = "'.$COURSES['curs_href'].'";
  var video_duration  = "'.$originalTime.'";

  // When the video is loaded
  videoPlayer.on("loaded", function() {
    // Get video duration in seconds
    videoPlayer.getDuration().then(function(duration) {
      totalTime = duration;
      var threeFourthTime = (3 / 4) * totalTime;

      // Initialize the remaining time (for 1/4 countdown)
      timeRemaining = threeFourthTime;
      updateCountdownDisplay(timeRemaining);
    });
  });

  // When video is played
  videoPlayer.on("play", function() {
    console.log("Played the video");
    isPlaying = true;

    if (!countdownInterval) {
      startCountdown();
    }
  });

  // When video is paused
  videoPlayer.on("pause", function() {
    console.log("Paused the video");
    isPlaying = false;
    stopCountdown();
  });

  videoPlayer.setVolume(1); // Ensure video is not muted

  function startCountdown() {
    // Start the countdown interval only if time remains
    if (timeRemaining > 0) {
      countdownInterval = setInterval(function() {
        // Only count down if the video is playing
        if (isPlaying) {
          // Send AJAX request to track playing
          $.ajax({
            url        : "'.SITE_URL.'include/ajax/get_tracking.php",
            method     : "POST",
            data       : {
              "id_video"        : id_video,
              "id_week"         : id_week,
              "id_lecture"      : id_lecture,
              "id_curs"         : id_curs,
              "curs_href"       : curs_href,
              "id_mas"          : id_mas,
              "id_ad_prg"       : id_ad_prg,
              "video_duration"  : video_duration,
              "track_mood"      : "playing"
            },
            success    : function(e) {
              // If the track is added or already added, continue countdown
              if (e === "track_added" || e === "track_already_added") {
                timeRemaining--;
                updateCountdownDisplay(timeRemaining);

                // If the timeRemaining reaches 0, stop the countdown and mark as completed
                if (timeRemaining <= 1) {
                  stopCountdown();
                  markAsCompleted();
                }
              } else if (e === "track_already_added_dont_track") {
                // Optionally handle this case, e.g., stop tracking or do nothing
                console.log("Tracking not required");
              }
            }
          });
        }
      }, 1000); // Update every second
    }
  }

  function stopCountdown() {
    // Clear the interval to pause the countdown
    clearInterval(countdownInterval);
    countdownInterval = null;
  }

  function updateCountdownDisplay(time) {
    // Convert seconds to minutes and seconds
    var minutes = Math.floor(time / 60);
    var seconds = Math.floor(time % 60);

    // Format time display
    var formattedTime = minutes + ":" + (seconds < 10 ? "0" + seconds : seconds);
    document.getElementById("video_remaining_time").innerText = "Next lecture in: " + formattedTime;
  }

  function markAsCompleted() {
    // Send AJAX request to mark the video as completed
    $.ajax({
      url        : "'.SITE_URL.'include/ajax/get_tracking.php",
      method     : "POST",
      data       : {
        "id_video"    : id_video,
        "id_week"     : id_week,
        "id_lecture"  : id_lecture,
        "id_curs"     : id_curs,
        "curs_href"   : curs_href,
        "id_mas"      : id_mas,
        "id_ad_prg"   : id_ad_prg,
        "track_mood"  : "completed"
      },
      success    : function(response) {
        // Display the response in the video_remaining_time div
        document.getElementById("video_remaining_time").innerHTML = response;
      }
    });
  }
</script>';
?>