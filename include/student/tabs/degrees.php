<?php
if(isset($view) && !empty($view)){
    include ($fileName.'/detail.php');
}else{
    include ($fileName.'/list.php');
}
?>