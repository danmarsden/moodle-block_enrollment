<?php
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->dirroot . '/enrol/manual/locallib.php');

$user = optional_param('users', null, PARAM_INT);
$role = optional_param('roles', null, PARAM_INT);
$courses = optional_param_array('courses', null, PARAM_INT);
$datestart = optional_param('datestart', null, PARAM_RAW);
$dateend = optional_param('dateend', null, PARAM_RAW);

$context = context_system::instance();

if (!has_capability('blocks/enrollment:viewpage', $context)) {
    print_error(get_string('notallowed', 'block_enrollment'));
}

$url = new moodle_url('/blocks/enrollment/enrollment.php');

require_login();

$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'block_enrollment'));
$PAGE->set_heading(get_string('pluginname', 'block_enrollment'));

if (!empty($datestart)) {
    $datestart = strtotime($datestart);
}
if (!empty($dateend)) {
    $dateend = strtotime($dateend);
}
if (!empty($datestart) && !empty($dateend) && ($dateend < $datestart)) {
    print_error('La date de fin doit etre supérieure à la date de début', null, $CFG->wwwroot . '/blocks/enrollment/enrollment.php');
}

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->js("/blocks/enrollment/js/json.js");
$PAGE->requires->js_init_call('M.block_enrollment.init');

echo $OUTPUT->header();

enrollment_display_roles();

if (!empty($user) && !empty($courses)) {
    for ($i = 0; $i < count($courses); $i++) {
        $course = $DB->get_record('course', array('id' => $courses[$i]));
        enrollment_enrol_user($user, $courses[$i], $role, $datestart, $dateend);
    }
}
?>
<h3><?php echo get_string('pluginname', 'block_enrollment') ?></h3>
<div style="padding : 5px;">
    <form name ="form" id="form" method="POST" action="#">
        <div>
            <div style="width:50%;float:left;text-align : center">
                <label for="users"><?php echo get_string('users', 'block_enrollment') ?> : </label>
                <select id="insc_users" name="users">
                    <?php
                    echo enrollment_display_users_options();
                    ?>
                </select>
            </div>

            <div style="width:50%;float:left;text-align : center">
                <label for="roles"><?php echo get_string('roles', 'block_enrollment') ?> : </label>
                <select id="roles" name="roles">
                    <?php
                    echo enrollment_display_roles();
                    ?>
                </select>
            </div>
        </div>
        <div style="text-align : center;">
            <label for="courses"><?php echo get_string('courses', 'block_enrollment') ?> : </label>
            <select id="courses" name="courses[]" size="10" multiple="multiple">
            </select>
        </div>
        <div id="dates">
            <div style="width:50%;float:left;text-align : center">
                <label for="datestart">Date de début (facultatif) : </label><input type="text" id="datestart" name="datestart">
            </div>
            <div style="width:50%;float:left;text-align : center">
                <label for="dateend">Date de fin (facultatif) : </label><input type="text" id="dateend" name="dateend">
            </div>
        </div>
        <div style="text-align : center;margin-top : 85px;text-align : center;">
            <input id="valider" type="submit" value="Valider" />
        </div>
    </form>	
</div>
<?php
echo $OUTPUT->footer();

