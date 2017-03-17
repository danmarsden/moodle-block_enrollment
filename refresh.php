<?php
    require_once("../../config.php");
    require_once('lib.php');

    //TO DO: Is this code okay, or should it require a capability or is_siteadmin?

    $user = $_POST['user'];
    echo json_encode(enrollment_get_courses_list($user));
?>
