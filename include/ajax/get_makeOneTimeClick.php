<?php
session_start();
if ($_POST['click_method'] === "site") {
    echo $_SESSION[$_POST['curs_href']] = (isset($_SESSION[$_POST['curs_href']])?$_SESSION[$_POST['curs_href']]:0)+1;
} 
if ($_POST['click_method'] === "learn") {
    unset($_SESSION[$_POST['curs_href']]);
}
?>