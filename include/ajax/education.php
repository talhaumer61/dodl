<?php
    echo '
    <div class="row" id="rowResource">
        <div class="col-lg-3 form-group">
            <label class="form-label">Program </label>
            <input type="text" class="form-control" name="program[]" />
        </div>
        <div class="col-lg-3 form-group">
            <label class="form-label">Institute </label>
            <input class="form-control" type="text" name="institute[]" id="fileInput">
        </div>
        <div class="col-lg-3 form-group">
            <label class="form-label">Grade</label>
            <input class="form-control" type="text" name="grade[]" id="fileInput">
        </div>
        <div class="col-lg-2 form-group">
            <label class="form-label">Year</label>
            <input type="text" class="form-control" name="year[]"/>
        </div>
        <div class="col-md-1 d-flex align-items-center" style="margin-top: 12px;">
            <i class="fa-regular fa-circle-xmark" onclick="removeinput(this.id)" id="addResource'.$_POST['isrno'].'" style="font-size: 30px;"></i>
        </div>
    </div>  
    ';
?>