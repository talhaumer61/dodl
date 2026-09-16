<?php
include ('../../dbsetting/classdbconection.php');
include ('../../dbsetting/lms_vars_config.php');
include ('../../functions/functions.php');
include ('../../functions/login_func.php');
$dblms = new dblms();

$title = htmlspecialchars($_GET['title']);
$description = htmlspecialchars(strip_tags(substr(get_dataHashingOnlyExp($_GET['description'], false), 0, 150)));
$image = $_GET['image'];
$url = $_GET['url'];

echo'
<meta property="og:title" content="'.$title.'">
<meta property="og:description" content="'.$description.'">
<meta property="og:image" content="'.$image.'">
<meta property="og:url" content="' . $url.'">
<meta property="og:type" content="article">

<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content border-0">
    <div class="modal-header bg-success">
      <h6 class="modal-title text-white"><i class="feather-share-2"></i> Share Options</h6>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body text-center">

      <!-- 🔹 Preview Section -->
      <div class="share-preview d-flex align-items-center gap-3 p-3 border rounded mb-3 text-start">
        <img src="'.$image.'" alt="Preview" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
        <div class="flex-grow-1">
          <h6 class="mb-1">'.$title.'</h6>
          <p class="text-muted small mb-1">'.$description.'...</p>
          <a href="'.$url.'" target="_blank" class="text-primary small">'.$url.'</a>
        </div>
      </div>

      <!-- 🔹 Share Buttons -->
      <div class="share-buttons d-flex justify-content-center flex-wrap gap-3 mt-2" data-title="'.$title.'" data-url="'.$url.'">
        <a href="#" class="share-btn fb" data-platform="facebook" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="share-btn tw" data-platform="twitter" title="Share on Twitter"><i class="fab fa-twitter"></i></a>
        <a href="#" class="share-btn li" data-platform="linkedin" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" class="share-btn wa" data-platform="whatsapp" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
        <a href="#" class="share-btn em" data-platform="email" title="Share via Email"><i class="fa-regular fa-envelope"></i></a>
        <a href="#" class="share-btn copy" data-platform="copy" title="Copy Link"><i class="fa-regular fa-copy"></i></a>
      </div>

    </div>

    <div class="modal-footer">
      <div class="hstack gap-2 mx-auto">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
          <i class="far fa-times-circle me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>';
?>


<script>
function initBlogShare(context = document) {
  context.querySelectorAll(".share-buttons").forEach(container => {
    const title = encodeURIComponent(container.dataset.title || document.title);
    const url = encodeURIComponent(container.dataset.url || window.location.href);

    container.querySelectorAll(".share-btn").forEach(btn => {
      btn.addEventListener("click", function(e) {
        e.preventDefault();
        const platform = this.dataset.platform;
        let shareUrl = "";

        switch (platform) {
          case "facebook":
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
          case "twitter":
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            break;
          case "linkedin":
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            break;
          case "whatsapp":
            shareUrl = `https://wa.me/?text=${title}%20${url}`;
            break;
          case "email":
            shareUrl = `mailto:?subject=${title}&body=${url}`;
            break;
          case "copy":
            navigator.clipboard.writeText(decodeURIComponent(url));
            alert("🔗 Blog link copied to clipboard!");
            return;
        }

        if (shareUrl) {
          window.open(shareUrl, "_blank", "width=600,height=500,left=100,top=100");
        }
      });
    });
  });
}

// Run once on page load
initBlogShare();
</script>