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

define('MAX_ACCOUNTS_LIMIT', 15); //max test-user accounts a user can create

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

//initialise class
$class = new testaccount_automation_create();

// check if user has already exhausted on number of test accounts allowed to create. max limit-15
$limitexceeds = $class->testaccount_automation_checklimitexceeds($courseadmin);
if($limitexceeds){
    echo $OUTPUT->header();
    
    $a = new stdClass();
    $a->count = $limitexceeds;
    $a->username = $courseadmin->username;
    echo $OUTPUT->heading(get_string('limitexceednotification', 'local_testaccount_automation', $a));
    
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
    
    $returndata = $class->testaccount_automation_processuserdata($from_testaccountform, $courseadmin);
    
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('successnotification', 'local_testaccount_automation', $courseadmin->username));
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    //echo html_writer::tag('p', get_string('successnotification', 'local_testaccount_notification'));
    $table = testaccount_automation_printtable($returndata);
    echo html_writer::table($table);
    
    //---- display pwd for test-user accounts
    //Not ideal to display plaintext password on screen. But, better than sending a plain text password via email...
    //TODO:: remove this hack once we develop feature to allow users to edit their test-accounts passwords
    $tmppwd = $from_testaccountform->testaccountpwd;
    echo $OUTPUT->notification(get_string('pwdplaintext', 'local_testaccount_automation', $tmppwd));
    echo '<br /><br />';
    //----------
    
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