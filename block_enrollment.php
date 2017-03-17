<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/enrollment/lib.php');

class block_enrollment extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_enrollment');
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        // shortcut -  only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }
        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = '<a href="' . $CFG->wwwroot . '/blocks/enrollment/enrollment.php">' . get_string('enrollusers', 'block_enrollment') . '</a>';

        return $this->content;
    }

}
