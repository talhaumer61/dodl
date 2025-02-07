<?php
echo '
<form method="post" id="discussion">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Submit Disscussion ( '.$_GET['subject'].' )</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="id_discussion" value="'.$_GET['id'].'">
        <textarea name="dst_detail" class="form-control" rows="10"></textarea>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="discussion_submit" class="btn btn-primary">Submit</button>
    </div>
</form>
<script>
$("#discussion").submit(function(event) {
    event.preventDefault();
    let formData = $(this).serialize();
    let id = $(this).find("[name=\'id_discussion\']").val()
    $.ajax({
        type: "POST",
        url:  window.location.href,
        data: formData,
        success: function(response) {
            $("#Modal").modal("hide");
            Toastify({
                newWindow: !0,
                text: "Successfully! Submitted Discussion",
                gravity: "top",
                position: "right",
                className: "bg-success",
                stopOnFocus: !0,
                offset: "50",
                duration: "2000",
                close: "close",
                style: "style",
            }).showToast();
            $("[data-id="+id+"]").html("<button class=\'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded\'>Submitted</button>");
        },
        error: function(error) {
            console.error("Error:", error);
        }
    });
});
</script>
';
?>