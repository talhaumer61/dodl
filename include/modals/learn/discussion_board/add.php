<?php
echo'
<script src="assets/js/app.js"></script>
<div class="modal-header bg-info p-3">
    <h5 class="modal-title" id="exampleModalLabel"><i class="ri-add-circle-line align-bottom me-1"></i>Submit Your Discussion</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
</div>
<form autocomplete="off" class="form-validate"  enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div class="modal-body">
    <input type="hidden" name="id_discussion" value="'.$_GET['id_discussion'].'">
    <div class="row">
        <div class="col">
            <div class="mb-2">
                <label class="form-label">Discussion</label>
                <textarea name="dst_detail" class="form-control" rows="10" required=""></textarea>
            </div>
        </div>
    </div>
    </div>
    <div class="modal-footer">
        <div class="hstack gap-2 justify-content-end">
            <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="submit_discussion" class="btn btn-info">Submit</button>
        </div>
    </div>
</form>';
?>