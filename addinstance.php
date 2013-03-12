<?php

require('../../config.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

require_login($course);
require_capability('moodle/course:enrolconfig', $context);
require_sesskey();

$enrol = enrol_get_plugin('condition');

if ($enrol->get_newinstance_link($course->id)) {
    $enrol->add_default_instance($course);
}

redirect(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));

?>
