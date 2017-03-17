<?php
require_once("../../config.php");
require_once('lib.php');

$user = required_param('user', PARAM_INT);

require_login();
require_capability('blocks/enrollment:viewpage', context_system::instance());

echo json_encode(enrollment_get_courses_list($user));