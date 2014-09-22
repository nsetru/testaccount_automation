<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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

$course     = required_param('id',PARAM_INT);

global $PAGE, $OUTPUT, $USER;

require_login();

$redirecturl = new moodle_url('/course/view.php', array('id' => $course));

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/testaccount_automation/index.php');
$PAGE->set_title(get_string('pluginname','local_testaccount_automation'));
$PAGE->set_heading('Test');


//display a form
$testaccountform = new testaccount_automation_form();
$testaccountform->set_data(array('testaccountemail' => $USER->email));

if ($testaccountform->is_cancelled()){
    redirect($redirecturl);
} else if ($from_testaccountform = $testaccountform->get_data()){
    //get data for form
} else {
    
    
}
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('outputheading', 'local_testaccount_automation'));
$testaccountform->display();
echo $OUTPUT->footer();