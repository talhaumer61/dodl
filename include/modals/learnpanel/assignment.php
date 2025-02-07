<?php
echo '
<form method="post" id="assignment" enctype="multipart/form-data">
    <div class="modal-header bg-info">
        <h5 class="modal-title text-white" id="exampleModalLabel">Submit Assignment ( '.$_GET['subject'].' )</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <input type="" name="id_assignment" value="'.$_GET['id'].'">
        <input type="" name="id_curs" value="'.$_GET['id_curs'].'">
        <input type="" name="id_mas" value="'.$_GET['id_mas'].'">
        <input type="" name="id_ad_prg" value="'.$_GET['id_ad_prg'].'">
        <input type="" name="name" value="'.$_GET['subject'].'">

        <div class="row">
            <div class="col">
                <div class="mb-2">
                    <label class="form-label">Description</label>
                    <textarea name="student_reply" class="form-control" rows="5">afds</textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="mb-2">
                    <label class="form-label">Attach File <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" accept=".pdf,.xlsx,.xls,.doc,.docx,.ppt,.pptx,.png,.jpg,.jpeg,.rar,.zip" id="fileInput" name="student_file" required>
                    <p id="errorMessage" class="text-danger mt-3" style="display: none;">File must be less than 5MB.</p>
                    <div class="text-primary" style="font-size : 0.8rem;">Upload valid files. Only <span class="text-danger fw-bold">pdf, xlsx, xls, doc, docx, ppt, pptx, png, jpg, jpeg, rar, zip</span> are allowed.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="assignment_submit" class="btn btn-info">Submit</button>
    </div>
</form>
<script>
document.getElementById("fileInput").addEventListener("change", function() {
    var maxFileSize = 5 * 1024 * 1024;
    var errorMessage = document.getElementById("errorMessage");
    var selectedFile = this.files[0];

    if (selectedFile && selectedFile.size > maxFileSize) {
        errorMessage.style.display = "block";
        this.value = "";
    } else {
        errorMessage.style.display = "none";
    }
});
$("#assignment").submit(function(event) {
    event.preventDefault();
    let formData = new FormData($(this)[0]);
    console.log(formData);
    let id = $(this).find("[name=\'id_assignment\']").val();
    $.ajax({
        type: "POST",
        url:  window.location.href,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $("#Modal").modal("hide");
            Toastify({
                newWindow: !0,
                text: "Successfully! Submitted Assignment",
                gravity: "top",
                position: "right",
                className: "bg-success",
                stopOnFocus: !0,
                offset: "50",
                duration: "2000",
                close: "close",
                style: "style",
            }).showToast();
            $("[data-id="+id+"]").html("<button class=\'btn btn-primary\'>Submitted</button>");
        },
        error: function(error) {
            console.error("Error:", error);
        }
    });
});
</script>
';

?>