<?php
$originalTime = 0;
if (isset($videoInfo['duration'])) {
    $originalTime = seconds_to_time($videoInfo['duration']);
}
echo '
<script>
$(function () {

  /* ================= DEBUG MODE ================= */
  const DEBUG = true; // ❗ set FALSE after testing

  function debug(label, data = "") {
    if (!DEBUG) return;
    console.log("📍 [VIDEO TRACK]", label, data);
  }

  debug("Script loaded");

  /* ================= ELEMENTS ================= */
  const timerEl = document.getElementById("video_remaining_time");
  if (!timerEl) {
    debug("Timer element not found");
    return;
  }

  timerEl.innerText = "Loading lesson timer...";

  const iframe = document.querySelector("iframe");
  if (!iframe) {
    debug("Iframe NOT found");
    timerEl.innerText = "Video not loaded";
    return;
  }

  debug("Iframe found");

  const player = new Vimeo.Player(iframe);

  /* ================= STATE ================= */
  let totalTime = 0;
  let timeRemaining = 0;
  let interval = null;
  let isPlaying = false;
  let isInitialized = false;
  let isCompleted = false;
  let lastTick = null;
  let ajaxTick = 0;

  /* ================= PAYLOAD ================= */
  const payload = {
    id_video       : "'.$COURSES_LESSONS['lesson_video_code'].'",
    id_week        : "'.$COURSES_LESSONS['id_week'].'",
    id_lecture     : "'.$COURSES_LESSONS['lesson_id'].'",
    id_curs        : "'.$COURSES['curs_id'].'",
    id_mas         : "'.$COURSES['id_mas'].'",
    id_ad_prg      : "'.$COURSES['id_ad_prg'].'",
    curs_href      : "'.$COURSES['curs_href'].'",
    video_duration : "'.$originalTime.'"
  };

  debug("Payload ready", payload);

  /* ================= VIDEO LOADED ================= */
  player.on("loaded", async function () {
    try {
      totalTime = await player.getDuration();
      timeRemaining = Math.ceil(totalTime * 0.75);
      isInitialized = true;

      debug("Video loaded", {
        totalTime,
        watchRequired: timeRemaining
      });

      updateUI();
    } catch (e) {
      debug("Vimeo load error", e);
    }
  });

  /* ================= PLAY / PAUSE ================= */
  player.on("play", function () {
    debug("Play event");
    if (!isInitialized || isCompleted) return;
    isPlaying = true;
    startTimer();
  });

  player.on("pause", function () {
    debug("Pause event");
    isPlaying = false;
    stopTimer();
  });

  document.addEventListener("visibilitychange", () => {
    if (document.hidden && isPlaying) {
      debug("Tab hidden — pausing");
      player.pause();
      stopTimer();
    }
  });

  /* ================= TIMER ================= */
  function startTimer() {
    if (interval || timeRemaining <= 0) return;

    debug("Timer started");
    lastTick = Date.now();

    interval = setInterval(() => {
      if (!isPlaying || isCompleted) return;

      const now = Date.now();
      const diff = Math.floor((now - lastTick) / 1000);

      if (diff >= 1) {
        timeRemaining -= diff;
        lastTick = now;

        debug("Timer tick", timeRemaining);

        if (timeRemaining <= 0) {
          timeRemaining = 0;
          stopTimer();
          completeLesson();
        }

        updateUI();
      }

      ajaxTick += diff;
      if (ajaxTick >= 5) {
        ajaxTick = 0;
        sendTracking();
      }

    }, 500);
  }

  function stopTimer() {
    debug("Timer stopped");
    clearInterval(interval);
    interval = null;
    lastTick = null;
  }

  /* ================= UI ================= */
  function updateUI() {
    let m = Math.floor(timeRemaining / 60);
    let s = timeRemaining % 60;
    timerEl.innerText = "Next lecture in: " + m + ":" + String(s).padStart(2, "0");
  }

  /* ================= TRACKING ================= */
  function sendTracking() {
    debug("Sending tracking (playing)");

    $.post("'.SITE_URL.'include/ajax/get_tracking.php", {
      ...payload,
      track_mood: "playing"
    })
    .done(res => {
      res = (res || "").trim();
      debug("Tracking response", res);

      if (res.startsWith("OK:")) {
        // ✅ Safe
        return;
      }

      // ❌ Silent DB failure or logic error
      debug("Tracking ERROR detected", res);
      stopTimer();
      showTrackingError(res);
    })
    .fail((xhr, status, err) => {
      debug("Tracking AJAX FAILED", {
        status,
        err,
        response: xhr.responseText
      });
      stopTimer();
      showTrackingError("AJAX_FAILED");
    });
  }

  function showTrackingError(reason) {
    timerEl.innerHTML = `
      <span style="color:red">
        Tracking failed (${reason})
        <button id="retryTrack">Retry</button>
      </span>
    `;

    document.getElementById("retryTrack").onclick = () => {
      debug("Retry tracking clicked");
      startTimer();
    };
  }

  /* ================= COMPLETE ================= */
  function completeLesson() {
    if (isCompleted) return;
    isCompleted = true;

    debug("Completing lesson");

    $.post("'.SITE_URL.'include/ajax/get_tracking.php", {
      ...payload,
      track_mood: "completed"
    })
    .done(res => {
      debug("Completion response", res);

      if (res) {
        timerEl.innerHTML = res;
      } else {
        showRetry();
      }
    })
    .fail((xhr, status, err) => {
      debug("Completion FAILED", {
        status,
        err,
        response: xhr.responseText
      });
      showRetry();
    });
  }

  function showRetry() {
    timerEl.innerHTML = `
      <span style="color:red">
        Lesson completion failed.
        <button id="retryLesson">Retry</button>
      </span>`;
    document.getElementById("retryLesson").onclick = completeLesson;
  }

});
</script>';

/*
$originalTime = 0;
if (isset($videoInfo['duration'])) {
    $originalTime = seconds_to_time($videoInfo['duration']);
}
echo '
<script>
$(function () {

  const timerEl = document.getElementById("video_remaining_time");
  timerEl.innerText = "Loading lesson timer...";

  const iframe = document.querySelector("iframe");
  if (!iframe) return;

  const player = new Vimeo.Player(iframe);

  let totalTime = 0;
  let timeRemaining = 0;
  let interval = null;
  let isPlaying = false;
  let isInitialized = false;
  let isCompleted = false;

  const payload = {
    id_video       : "'.$COURSES_LESSONS['lesson_video_code'].'",
    id_week        : "'.$COURSES_LESSONS['id_week'].'",
    id_lecture     : "'.$COURSES_LESSONS['lesson_id'].'",
    id_curs        : "'.$COURSES['curs_id'].'",
    id_mas         : "'.$COURSES['id_mas'].'",
    id_ad_prg      : "'.$COURSES['id_ad_prg'].'",
    curs_href      : "'.$COURSES['curs_href'].'",
    video_duration : "'.$originalTime.'"
  };

  // === INIT TIMER AFTER VIDEO LOAD ===
  player.on("loaded", async function () {
    totalTime = await player.getDuration();
    timeRemaining = Math.ceil(totalTime * 0.75);
    isInitialized = true;
    updateUI();
  });

  player.on("play", function () {
    if (!isInitialized || isCompleted) return;
    isPlaying = true;
    startTimer();
  });

  player.on("pause", function () {
    isPlaying = false;
    stopTimer();
  });

  document.addEventListener("visibilitychange", () => {
    if (document.hidden && isPlaying) {
      player.pause();
      stopTimer();
    }
  });

  let lastTick = null;
  let ajaxTick = 0;

  function startTimer() {
    if (interval || timeRemaining <= 0) return;

    lastTick = Date.now();

    interval = setInterval(() => {
      if (!isPlaying || isCompleted) return;

      const now = Date.now();
      const diff = Math.floor((now - lastTick) / 1000);

      if (diff >= 1) {
        timeRemaining -= diff;
        lastTick = now;

        if (timeRemaining <= 0) {
          timeRemaining = 0;
          stopTimer();
          completeLesson();
        }

        updateUI();
      }

      // 🔁 Send tracking every 5 seconds only
      ajaxTick += diff;
      if (ajaxTick >= 5) {
        ajaxTick = 0;
        sendTracking();
      }

    }, 500);
  }

  function sendTracking() {
    $.post("'.SITE_URL.'include/ajax/get_tracking.php", {
      ...payload,
      track_mood: "playing"
    });
  }

  function stopTimer() {
    clearInterval(interval);
    interval = null;
    lastTick = null;
  }

  function updateUI() {
    let m = Math.floor(timeRemaining / 60);
    let s = timeRemaining % 60;
    timerEl.innerText = "Next lecture in: " + m + ":" + String(s).padStart(2, "0");
  }

  function completeLesson() {
    if (isCompleted) return;
    isCompleted = true;

    $.post("'.SITE_URL.'include/ajax/get_tracking.php", {
      ...payload,
      track_mood: "completed"
    })
    .done(res => {
      if (res) {
        timerEl.innerHTML = res;
      } else {
        showRetry();
      }
    })
    .fail(showRetry);
  }

  function showRetry() {
    timerEl.innerHTML = `
      <span class="text-red-600">
        Lesson completion failed.
        <button id="retryLesson" class="btn btn-sm btn-warning ml-2">
          Retry
        </button>
      </span>`;
    document.getElementById("retryLesson").onclick = completeLesson;
  }

});
</script>';
*/