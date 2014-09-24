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

//$course     = required_param('id',PARAM_INT);
$courseid = optional_param('course', 0, PARAM_INT);

global $PAGE, $OUTPUT, $USER;

require_login();

$redirecturl = new moodle_url('/course/view.php', array('id' => $courseid));

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/testaccount_automation/index.php');
$PAGE->set_title(get_string('pluginname','local_testaccount_automation'));
$PAGE->set_heading('Test');


//display a form
$testaccountform = new testaccount_automation_form(null, array('courseid' => $courseid));
$testaccountform->set_data(array('testaccountemail' => $USER->email));

if ($testaccountform->is_cancelled()){
    redirect($redirecturl);
} else if ($from_testaccountform = $testaccountform->get_data()){
    $courseadmin = (object)$courseadmin;
    $courseadmin->username = $USER->username;
    $courseadmin->id = $USER->id;
    //get data for form
    $data = testaccount_automation_processuserdata($from_testaccountform, $courseadmin);
    //echo $data;
    print '<pre>';
    var_dump($data);
    print '</pre>';
    
} else {
    
    
}
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('outputheading', 'local_testaccount_automation'));
$testaccountform->display();
echo $OUTPUT->footer();