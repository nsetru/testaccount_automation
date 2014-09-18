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
        
        $options1 = array (
            '5' => '5 days',
            '10' => '10 days',
            '15' => '15 days'
        );
        $mform->addElement('select', 'numofdays', get_string('numofdays','local_testaccount_automation'), $options1);
        
        $mform->addElement('passwordunmask', 'testaccoutpwd', get_string('testaccoutpwd','local_testaccount_automation'), 'size="20"');
        //$mform->addHelpButton('testaccoutpwd', 'testaccoutpwd');
        $mform->setType('testaccoutpwd', PARAM_RAW);
    }
    
}
