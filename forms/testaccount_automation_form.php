<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class testaccount_automation_form extends moodleform {
    
    function definition() {
        global $CFG;
        
        $mform = $this->_form;
        $courseid = $this->_customdata['courseid'];
        
        //$mform->addElement('header', 'settingsheader', 'Details for Test accounts');
        $options = array (
            '1' => '1',
            '5' => '5',
            '10' => '10',
            '15' => '15'
        ) ;
        $mform->addElement('select', 'numtestaccounts', get_string('numtestaccounts','local_testaccount_automation'), $options);
        $mform->addHelpButton('numtestaccounts', 'numtestaccounts', 'local_testaccount_automation');
        
        $options1 = array (
            '1' => '1 day',
            '5' => '5 days',
            '10' => '10 days',
            '15' => '15 days'
        );
        $mform->addElement('select', 'numofdays', get_string('numofdays','local_testaccount_automation'), $options1);
        $mform->addHelpButton('numofdays', 'numofdays', 'local_testaccount_automation');
        
        if (!empty($CFG->passwordpolicy)){
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        
        $mform->addElement('passwordunmask', 'testaccountpwd', get_string('testaccountpwd','local_testaccount_automation'), 'size="20"');
        $mform->addHelpButton('testaccountpwd', 'testaccountpwd', 'local_testaccount_automation');
        $mform->addRule('testaccountpwd', get_string('required'), 'required', null, 'client');
        $mform->setType('testaccountpwd', PARAM_RAW);
        
        $mform->addElement('text', 'testaccountemail', get_string('testaccountemail','local_testaccount_automation'), 'maxlength="100" size="30"');
        $mform->addHelpButton('testaccountemail', 'testaccountemail', 'local_testaccount_automation');
        $mform->addRule('testaccountemail', get_string('required'), 'required', null, 'client');
        $mform->setType('testaccountemail', PARAM_EMAIL);
        
        //$mform->addElement('select', 'courses', get_string('courses','local_testaccount_automation'), $options);
        /*$buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');*/
        /*$mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $courseid);*/
        $mform->addElement('hidden', 'course', null);
        $mform->setType('course', PARAM_INT);
        $mform->setDefault('course', $courseid);
        
        $this->add_action_buttons(true);
    }
    
    function validation($data, $files) {
        parent::validation($data, $files);
        
        $data = (object)$data;
        $err = array();
        
        if (!empty($data->testaccountpwd)) {
            $error = '';
            if(!check_password_policy($data->testaccountpwd, $error)){
                $err['testaccountpwd'] = $error;
            }
        }
            
        if(!empty($data->testaccountemail)){
            if (!validate_email($data->testaccountemail)) {
                $err['testaccountemail'] = get_string('invalidemail');
            }
        }
        
        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
            
    }
}
