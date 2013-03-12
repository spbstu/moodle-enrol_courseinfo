<?php

require_once($CFG->dirroot.'/course/lib.php');

defined('MOODLE_INTERNAL') || die();

class enrol_condition_plugin extends enrol_plugin {
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'condition') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = get_context_instance(CONTEXT_COURSE, $instance->courseid);

        $icons = array();

        if (has_capability('moodle/course:enrolconfig', $context)) {
            $editlink = new moodle_url("/enrol/condition/edit.php", array('courseid'=>$instance->courseid));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('i/edit', get_string('edit'), 'core', array('class'=>'icon')));
        }

        return $icons;
    }

    public function get_info_icons(array $instances) {

        $icons = array();
        $icons[] = new pix_icon('icon', get_string('pluginname', 'enrol_condition'), 'enrol_condition');
        return $icons;
    }

    public function enrol_page_hook(stdClass $instance) {
	    global $DB, $OUTPUT, $USER;

	    $courseid = $instance->courseid;

        $user = $DB->get_record('user', array('id'=>$USER->id), '*');
        if(empty($user->idnumber)) {
            $instance = $DB->get_record('enrol', array('courseid'=>$courseid, 'enrol'=>'self'), '*');

            if($instance) {
                $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);

                if (is_enrolled($context)) {
                    $plugin = enrol_get_plugin($instance->enrol);
                    $plugin->unenrol_user($instance, $USER->id);

                    $me = $DB->get_record('enrol', array('courseid'=>$courseid, 'enrol'=>'condition'), '*');
                    ob_start();
echo <<< EOF
<p>Заполнение акеты участника <span style="color: #ff0000;"><strong>обязательно</strong> </span>и является допуском к заданиям заочного тура олимпиады. Вернитесь к <a href="$me->customtext1">шагу 2</a>
<img src="http://dl.spbstu.ru/pluginfile.php/20196/course/summary/two.gif" alt="Шаг 2" style="vertical-align: middle; margin-left: 5px; margin-right: 5px;" title="Шаг 2" width="24" height="24" /></p>
EOF;
                    $output = ob_get_clean();

                    return $OUTPUT->box($output);
                }
            }
        }

        return;
    }

    public function get_newinstance_link($courseid) {
        global $DB;
        $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/guest:config', $context)) {
            return NULL;
        }

        if ($DB->record_exists('enrol', array('courseid'=>$courseid, 'enrol'=>'condition'))) {
            return NULL;
        }

        return new moodle_url('/enrol/condition/addinstance.php', array('sesskey'=>sesskey(), 'id'=>$courseid));
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
