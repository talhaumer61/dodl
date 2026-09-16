<?php

echo '
<form method="post">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Delete Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
    <h3 align="center">Are you sure you want to delete profile.</h3>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-info text-white" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="delete_profile" class="btn btn-danger">Delete Profile</button>
    </div>
</form>
';

?>