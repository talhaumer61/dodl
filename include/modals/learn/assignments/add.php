<?php
echo'
<script src="assets/js/app.js"></script>
<div class="modal-header bg-success p-2">
    <h6 class="modal-title text-white" id="exampleModalLabel"><i class="ri-add-circle-line align-bottom me-1"></i>Submit Your Assignment</h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
</div>
<form autocomplete="off" class="form-validate"  enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div class="modal-body">
    <input type="hidden" name="id_assignment" value="'.$_GET['id_assignment'].'">
    <input type="hidden" name="id_lecture" value="'.$_GET['id_lecture'].'">
    <input type="hidden" name="caption" value="'.$_GET['caption'].'">
    <input type="hidden" name="id_week" value="'.$_GET['id_week'].'">
    <input type="hidden" name="id_curs" value="'.$_GET['id_curs'].'">
    <input type="hidden" name="id_mas" value="'.$_GET['id_mas'].'">
    <input type="hidden" name="id_ad_prg" value="'.$_GET['id_ad_prg'].'">
    <div class="row">
        <div class="col">
            <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="student_reply" class="form-control" rows="5" required=""></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-2">
                <label class="form-label">Attach File <span class="text-danger">*</span></label>
                <input class="form-control" type="file" accept=".pdf, .xlsx, .xls, .doc, .docx, .ppt, .pptx, .png, .jpg, .jpeg" id="fileInput" name="student_file" required="">
                <p id="errorMessage" class="text-danger mt-3" style="display: none;">File must be less than 5MB.</p>
                <div class="text-primary" style="font-size : 0.8rem;">Upload valid files. Only <span class="text-danger fw-bold">pdf, xlsx, xls, doc, docx, ppt, pptx, png, jpg, jpeg</span> are allowed.</div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal-footer">
        <div class="hstack gap-2 justify-content-end">
            <button type="button" class="btn btn-danger btn-sm text-white" data-bs-dismiss="modal"><i class="fa fa-close me-1"></i>Close</button>
            <button type="submit" name="submit_assignment" class="btn btn-sm btn-success"><i class="fa fa-check me-1"></i>Submit</button>
        </div>
    </div>
</form>';
?>