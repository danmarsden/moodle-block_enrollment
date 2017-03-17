<?php

function enrollment_display_courses_options($userid) {
    $courses = enrollment_get_courses_list($userid);
    foreach ($courses as $course) {
        $options .= '<option value="' . $course->id . '">' . $course->shortname . '</option>';
    }
    return $options;
}

function enrollment_get_courses_list($userid) {
    global $DB;
    $req = "
            SELECT DISTINCT (c.id), c.shortname
            FROM {course} c
            WHERE
               c.id != 1
               AND
               c.id NOT IN (
                    SELECT DISTINCT(c.id)
                    FROM {enrol} e, {course} c, {user_enrolments} ue
                    WHERE
                        ue.userid = " . $userid . "
                        AND ue.enrolid = e.id
                        AND e.courseid = c.id
                )
        ";
    $courses = array();
    $result = $DB->get_records_sql($req);
    foreach ($result as $course) {
        $courses[] = $course;
    }
    return $courses;
}

function enrollment_display_users_options() {
    $users = enrollment_get_users_list();
    $options = '';
    foreach ($users as $user) {
        $options .= '<option value="' . $user->id . '">' . $user->lastname . ' ' . $user->firstname . '</option>';
    }
    return $options;
}

function enrollment_get_users_list() {
    global $DB;
    $select = "id != 1 ORDER BY lastname";
    return $DB->get_records_select('user', $select);
}

function enrollment_enrol_user($userid, $courseid, $role, $timestart, $timeend) {
    global $DB, $CFG;
    $instance = $DB->get_record('enrol', array('enrol' => 'manual', 'courseid' => $courseid));
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    if (!$enrol_manual = enrol_get_plugin('manual')) {
        throw new coding_exception('Can not instantiate enrol_manual');
    }

    if (!empty($timestart) && !empty($timeend) && $timeend < $timestart) {
        print_error('La date de fin doit etre supérieure à la date de début', null, $CFG->wwwroot . '/blocks/enrollment/enrollment.php');
    }
    if (empty($timestart)) {
        $timestart = $course->startdate;
    }
    if (empty($timeend)) {
        $timeend = 0;
    }
    $enrol_manual->enrol_user($instance, $userid, $role, $timestart, $timeend);
}

function enrollment_get_role_name($roleid) {
    global $DB;
    $sql = '
        SELECT
            *
        FROM
            {role}
        WHERE
            id= ' . $roleid;
    return $DB->get_record_sql($sql);
}

function enrollment_get_roles() {
    $roles = array();
    $rolesContext = get_roles_for_contextlevels(CONTEXT_COURSE);
    foreach ($rolesContext as $roleContext) {
        $role = enrollment_get_role_name($roleContext);
        $roles[] = $role;
    }
    return $roles;
}

function enrollment_display_roles() {
    $roles = enrollment_get_roles();
    $options = '';
    foreach ($roles as $role) {
        $role->name = role_get_name($role);
        $selected = $role->id == 5 ? 'selected' : '';
        $options .= '<option value="' . $role->id . '" ' . $selected . '>' . $role->name . '</option>';
    }
    return $options;
}
