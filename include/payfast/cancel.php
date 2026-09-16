<?php
sessionMsg("Error","".$_GET['err_msg'].".","danger");
header("Location: ".SITE_URL."student/challans/", true, 301);
?>