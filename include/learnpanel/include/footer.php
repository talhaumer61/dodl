<?php
echo'
    </div>
    <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="Modal" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div id="modal_detail"></div>
        </div>
      </div>
    </div>
    <!-- Javascript -->
    <script src="'.SITE_URL.'assets/learn/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="'.SITE_URL.'assets/learn/js/uikit.js"></script>
    <script src="'.SITE_URL.'assets/learn/js/tippy.all.min.js"></script>
    <script src="'.SITE_URL.'assets/learn/js/simplebar.js"></script>
    <script src="'.SITE_URL.'assets/learn/js/custom.js"></script>
    <script src="'.SITE_URL.'assets/learn/js/bootstrap-select.min.js"></script>
    <script src="'.SITE_URL.'assets/learn/js/ionicons.js"></script>
    <script src="'.SITE_URL.'assets/js/bootstrap.bundle.min.js"></script>
    <script src="'.SITE_URL.'assets/js/plugin.js"></script>

    <!-- CUSTOM-PLAYER JS  -->
    <script src="'.SITE_URL.'assets/learn/js/owl.carousel.min.js"></script>
	<script src="'.SITE_URL.'assets/learn/js/slider-radio.js"></script>
	<script src="'.SITE_URL.'assets/learn/js/select2.min.js"></script>
	<script src="'.SITE_URL.'assets/learn/js/plyr.min.js"></script>
	<script src="'.SITE_URL.'assets/learn/js/main.js"></script>
    <script>
        function show_modal(modal_url) {
            $.ajax({
                url : modal_url,
                success : function(response){
                    $("#modal_detail").html(response)
                    $("#Modal").modal("show");
                }
            })
        }
      document.addEventListener(\'keydown\', function(event) {
        // ctrl+shift+c
        if (event.ctrlKey && event.shiftKey && event.keyCode === 67) {
          event.preventDefault();
        }
        // ctrl+shift+i
        if (event.ctrlKey && event.shiftKey && event.keyCode === 73) {
          event.preventDefault();
        }
        // ctrl+u
        if (event.ctrlKey && event.keyCode === 85) {
          event.preventDefault();
        }
        // ctrl+s
        if (event.ctrlKey && event.keyCode === 83) {
          event.preventDefault();
        }
        // ctrl+p 
        if (event.ctrlKey && event.keyCode === 80) {
          event.preventDefault();
        }
        // ctrl+c
        if (event.ctrlKey && event.keyCode === 67) {
          event.preventDefault();
        }
        // ctrl+v
        if (event.ctrlKey && event.keyCode === 86) {
          event.preventDefault();
        }
        // ctrl+e
        if (event.ctrlKey && event.keyCode === 69) {
          event.preventDefault();
        }
      });
      // right click disable
      // document.addEventListener(\'contextmenu\', function(event) {
      //   event.preventDefault();
      // });
    </script>
</body>
</html>';
?>