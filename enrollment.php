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
    $datestart = strtotime($datestart . " 00:00 GMT");
}
if (!empty($dateend)) {
    $dateend = strtotime($dateend . " 23:59 GMT");
} else {
    $dateend = 0; //If End Date was left blank then we want to set it to 0 in the db which means 'no end date'
}

//TO DO: This does not appear to work!
if (!empty($datestart) && !empty($dateend) && ($dateend < $datestart) && $dateend != "0") {
    print_error('The End Date needs to be greater than the Start Date', null, $CFG->wwwroot . '/blocks/enrollment/enrollment.php');
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

$defaultStartDateText = date("m/d/Y"); //Today //TO DO: Does this need to be different format depending on country or is jquery datepicker always the same

?>
    <p><b><?php echo get_string('pluginname', 'block_enrollment') ?></b> - <?php echo get_string('description', 'block_enrollment') ?></p>
    <div style="padding:5px;">
        <form name ="form" id="form" method="POST" action="#">
            <div style="padding:20px;">

                <div style="display:inline-block;">
                    <div style="width:auto; float:left; padding:20px 20px 20px 20px;">
                        <div style="">
                            <label for="users"><?php echo get_string('users', 'block_enrollment') ?> : </label>
                            <select id="insc_users" name="users">
                                <?php
                                echo enrollment_display_users_options($user);
                                ?>
                            </select>
                        </div>
                        <br>
                        <div style="">
                            <label for="roles"><?php echo get_string('roles', 'block_enrollment') ?> : </label>
                            <select id="roles" name="roles">
                                <?php
                                echo enrollment_display_roles();
                                ?>
                            </select>
                        </div>
                    </div>

                    <div style="float:right; padding: 20px 20px 20px 50px;">
                        <label for="courses"><?php echo get_string('courses', 'block_enrollment') ?> : (<?php echo get_string('selectmultiple', 'block_enrollment') ?>)</label>
                        <select id="courses" name="courses[]" size="10" multiple="multiple" required="required" style="min-width:40%; max-width:80%;">
                        </select>
                    </div>
                </div>
                <br>

                <div id="dates" style="display:inline-block;">
                    <div style="width:auto; float:left; padding:20px 20px 20px 20px;">
                        <div style="">
                            <label for="datestart"><?php echo get_string('startdate', 'block_enrollment') ?> : </label><input type="text" id="datestart" name="datestart" value="<?php echo $defaultStartDateText; ?>">
                        </div>
                        <br>
                        <div style="">
                            <label for="dateend"><?php echo get_string('enddate', 'block_enrollment') ?> (<?php echo get_string('enddateleaveblank', 'block_enrollment') ?>) : </label><input type="text" id="dateend" name="dateend" value="">
                        </div>
                    </div>

                    <div style="float:right; padding: 20px 20px 20px 50px;">
                        <br>
                        <input id="valider" type="submit" value="<?php echo get_string('submit', 'block_enrollment') ?>" />
                    </div>
                </div>

            </div>
        </form>
    </div>
<?php
echo $OUTPUT->footer();
