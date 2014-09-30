<?php

/**
 * view
 *
 * @package    local
 * @subpackage testaccount_automation
 * @copyright  2014 UCL
 * @license    http://www.ucl.ac.uk
 */
require(dirname(__FILE__) . '/../../config.php');
require($CFG->dirroot.'/local/testaccount_automation/forms/testaccount_automation_form.php');
require($CFG->dirroot.'/local/testaccount_automation/lib.php');
require($CFG->dirroot.'/local/testaccount_automation/classes/testaccount_automation_create.php');

// we need courseid to know- which course user test accounts needs to be enrolled
$courseid = optional_param('course', 0, PARAM_INT);

global $PAGE, $OUTPUT, $USER;

require_login();

$redirecturl = new moodle_url('/course/view.php', array('id' => $courseid));

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/testaccount_automation/index.php');
$PAGE->set_title(get_string('pluginname','local_testaccount_automation'));
$PAGE->set_heading('Test Account Automation');


// set courseadmin details
$courseadmin = (object) $courseadmin;
$courseadmin->id = $USER->id;
$courseadmin->username = $USER->username;
$courseadmin->email = $USER->email;

// check if user has already exhausted on number of test accounts allowed to create. max limit-15
$class = new testaccount_automation_create();
$limitexceeds = $class->testaccount_automation_checklimitexceeds($courseadmin);
if($limitexceeds){
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('limitexceednotification', 'local_testaccount_automation', $courseadmin->username));
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    //echo html_writer::tag('p', get_string('successnotification', 'local_testaccount_notification'));
    $table = testaccount_automation_printalltestusers($courseadmin);
    echo html_writer::table($table);
    $actionurl = new moodle_url('/course/view.php', array('id' => $courseid));
    $continue = new single_button($actionurl, get_string('continue'), 'post');
    echo $OUTPUT->render($continue);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die;
}

//display a form
$testaccountform = new testaccount_automation_form(null, array('courseid' => $courseid));
$testaccountform->set_data(array('testaccountemail' => $USER->email));

if ($testaccountform->is_cancelled()){
    redirect($redirecturl);
} else if ($from_testaccountform = $testaccountform->get_data()){
    /*$courseadmin = (object)$courseadmin;
    $courseadmin->id = $USER->id;
    $courseadmin->username = $USER->username;
    $courseadmin->email = $USER->email;*/
    //process form data
    //$class = new testaccount_automation_create();
    $returndata = $class->testaccount_automation_processuserdata($from_testaccountform, $courseadmin);
    
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('successnotification', 'local_testaccount_automation', $courseadmin->username));
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    //echo html_writer::tag('p', get_string('successnotification', 'local_testaccount_notification'));
    $table = testaccount_automation_printtable($returndata);
    echo html_writer::table($table);
    $actionurl = new moodle_url('/course/view.php', array('id' => $courseid));
    $continue = new single_button($actionurl, get_string('continue'), 'post');
    echo $OUTPUT->render($continue);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die;
     
} 

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('outputheading', 'local_testaccount_automation'));
$testaccountform->display();
echo $OUTPUT->footer();