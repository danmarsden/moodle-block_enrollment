<?php
    require_once("../../config.php");
    require_once('lib.php');
    $user = $_POST['user'];
    echo json_encode(enrollment_get_courses_list($user));
?>
