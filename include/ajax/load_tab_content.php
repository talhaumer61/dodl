<?php
include "../dbsetting/lms_vars_config.php";
include "../dbsetting/classdbconection.php";
$dblms = new dblms();
include "../functions/functions.php";

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'description':
        include "course_detail/description.php";
        break;
    case 'course_content':
        include "course_detail/course_content.php";
        break;
    case 'instructors':
        include "course_detail/instructors.php";
        break;
    case 'how_it_works':
        include "course_detail/how_it_works.php";
        break;
    case 'enrollment':
        include "course_detail/enrollment.php";
        break;
    case 'faq':
        include "course_detail/faq.php";
        break;
    default:
        echo "No content available.";
}