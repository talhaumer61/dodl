<?php
include ('../../dbsetting/classdbconection.php');
include ('../../dbsetting/lms_vars_config.php');
include ('../../functions/functions.php');
include ('../../functions/login_func.php');
$dblms = new dblms();

echo'
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0">
        <div class="modal-header bg-info">
            <h6 class="modal-title text-white" id="exampleModalLabel"><i class="fas fa-stamp me-1"></i>Confirmation</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
        </div>        
        <form action="'.SITE_URL.'invoice" method="POST">
            <input type="hidden" name="url" value="'.$_GET['url'].'">
            <input type="hidden" name="id" value="'.$_GET['id'].'">
            <input type="hidden" name="type" value="'.$_GET['type'].'">
            <input type="hidden" name="curs_type_status" value="'.$_GET['curs_type_status'].'">
            <div class="modal-body">
                <p class="text-center">Would you like to reserve your seat in the upcoming batch?</p>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i class="far fa-times-circle me-1"></i>No</button>
                    <button type="submit" name="enroll" class="btn btn-sm btn-info text-white"><i class="fa fa-check me-1"></i>Yes</button>
                </div>
            </div>
        </form>
    </div>
</div>';
?>