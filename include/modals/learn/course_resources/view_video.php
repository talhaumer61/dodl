<?php
require_once ("../../../dbsetting/lms_vars_config.php");
require_once ("../../../dbsetting/classdbconection.php");
require_once ("../../../functions/functions.php");
$dblms = new dblms();

$condition = array(
                     'select'       =>  'id, embedcode'
                    ,'where'        =>  array(
                                                 'is_deleted'   => 0
                                                ,'id'           => cleanvars($_GET['view_id'])
                                            )
                    ,'return_type'  =>  'single'
);
$row = $dblms->getRows(COURSES_DOWNLOADS, $condition);
echo'
<script src="assets/js/app.js"></script>

<div class="modal-header bg-danger">
    <h6 class="modal-title text-white" id="exampleModalLabel"><i class="fab fa-youtube me-1"></i>Play Video</h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col mb-2">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/'.$row['embedcode'].'?rel=0" title="YouTube video" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="hstack gap-2 justify-content-end">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i class="far fa-times-circle me-1"></i>Close</button>
    </div>
</div>';