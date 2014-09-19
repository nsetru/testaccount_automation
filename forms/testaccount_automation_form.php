<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

class testaccount_automation_form extends moodleform {
    
    function definition() {
        global $CFG;
        
        $mform = $this->_form;
        
        //$mform->addElement('header', 'settingsheader', 'Details for Test accounts');
        $options = array (
            '5' => '5',
            '10' => '10',
            '15' => '15'
        ) ;
        $mform->addElement('select', 'numtestaccounts', get_string('numtestaccounts','local_testaccount_automation'), $options);
        $mform->addHelpButton('numtestaccounts', 'numtestaccounts', 'local_testaccount_automation');
        
        $options1 = array (
            '5' => '5 days',
            '10' => '10 days',
            '15' => '15 days'
        );
        $mform->addElement('select', 'numofdays', get_string('numofdays','local_testaccount_automation'), $options1);
        $mform->addHelpButton('numofdays', 'numofdays', 'local_testaccount_automation');
        
        $mform->addElement('passwordunmask', 'testaccoutpwd', get_string('testaccoutpwd','local_testaccount_automation'), 'size="20"');
        $mform->addHelpButton('testaccoutpwd', 'testaccoutpwd', 'local_testaccount_automation');
        $mform->addRule('testaccoutpwd', get_string('required'), 'required', null, 'client');
        $mform->setType('testaccoutpwd', PARAM_RAW);
        
        $mform->addElement('text', 'testaccountemail', get_string('testaccountemail','local_testaccount_automation'), 'maxlength="100" size="30"');
        $mform->addHelpButton('testaccountemail', 'testaccountemail', 'local_testaccount_automation');
        $mform->addRule('testaccountemail', get_string('required'), 'required', null, 'client');
        $mform->setType('testaccountemail', PARAM_EMAIL);
        
        $mform->addElement('select', 'courses', get_string('courses','local_testaccount_automation'), $options);
    }
    
    function validation($data, $files) {
        parent::validation($data, $files);
        
        
    }
}
