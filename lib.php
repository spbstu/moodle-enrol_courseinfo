<?php

require_once($CFG->dirroot.'/course/lib.php');

defined('MOODLE_INTERNAL') || die();

class enrol_info_plugin extends enrol_plugin {
    public function enrol_page_hook(stdClass $instance) {
	 global $DB, $OUTPUT;

	 $id = $instance->courseid;

         ob_start();
         $course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
	 print_course($course);
         $output = ob_get_clean();

         return $output;
    }

    public function get_newinstance_link($courseid) {
        global $DB;
        $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/guest:config', $context)) {
            return NULL;
        }

        if ($DB->record_exists('enrol', array('courseid'=>$courseid, 'enrol'=>'info'))) {
            return NULL;
        }

        return new moodle_url('/enrol/info/addinstance.php', array('sesskey'=>sesskey(), 'id'=>$courseid));
    }

    public function enrol_user(stdClass $instance, $userid,
                               $roleid = NULL,
                               $timestart = 0, $timeend = 0,
                               $status = NULL) {
        return;
    }

    public function unenrol_user(stdClass $instance, $userid) {
        return;
    }

    public function add_default_instance($course) {
        return $this->add_instance($course);
    }
}

?>
