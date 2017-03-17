<?php
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->dirroot.'/enrol/manual/locallib.php');

global $CFG, $USER, $SESSION, $COURSE, $DB;

$context = get_context_instance(CONTEXT_SYSTEM);

// boot if insufficient permission
if (!has_capability('blocks/enrollment:viewpage', $context)) {
    print_error(get_string('notallowed','block_enrollment'));
}

$url = new moodle_url('/blocks/enrollment/enrollment.php');

$PAGE->set_context($context);

$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname','block_enrollment'));
$PAGE->set_heading(get_string('pluginname','block_enrollment'));

$PAGE->requires->js("/blocks/enrollment/js/json.js");
$PAGE->requires->js_init_call('M.block_enrollment.init');
require_login();
echo $OUTPUT->header();

if (isset($_POST['users']) && isset($_POST['courses'])){
	$user = $_POST['users'];
	$courses = $_POST['courses'];
    $role = $_POST['roles'];
	for($i = 0;$i < count($courses);$i++){
        $course = $DB->get_record('course',array('id'=>$courses[$i]));
		enrollment_enrol_user($user,$courses[$i],$role);
	}
}
?>
	<h3><?php echo get_string('pluginname','block_enrollment') ?></h3>
	<div style="padding : 5px;">
		<form name ="form" id="form" method="POST" action="#">
			<label for="users"><?php echo get_string('users','block_enrollment') ?> : </label>
			<select id="insc_users" name="users" onchange="M.block_enrollment.refreshCourses(this)">
			<?php
				echo enrollment_display_users_options();
			?>
			<select>
			<br />
			<br />
			<label for="courses"><?php echo get_string('courses','block_enrollment') ?> : </label>
			<select id="courses" name="courses[]" size="10" multiple="multiple" style="width: 500px;">
			<select>
			<br />
			<br />
			<label for="roles"><?php echo get_string('roles','block_enrollment') ?> : </label>
            <select id="roles" name="roles">
			<?php
                echo enrollment_display_roles();
			?>
			<select>
			<br />
			<br />
			<input id="valider" type="submit" value="OK" />
		</form>	
	</div>
<?php

echo $OUTPUT->footer();

