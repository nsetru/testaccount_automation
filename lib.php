<?php

/**
 * library functions
 *
 * @package    local
 * @subpackage testaccount_automation
 * @copyright  2014 UCL
 * @license    http://www.ucl.ac.uk
 */

function testaccount_automation_processuserdata($testaccountdata, $courseadmin){
    $numtestaccounts = $testaccountdata->numtestaccounts;
    //validate test accounts before saving
   
    //generate username for test account
    //$testusernamearray = testaccount_automation_generateusername($courseadmin->username, $numtestaccounts);
    
    //create user within mdl_user table
    $i=1;
    while($i <= $numtestaccounts){
    //foreach($testusernamearray as $testuser){
        //set few default values
        $usernew->auth = 'manual';
        $usernew->deleted = 0;
        $usernew->mnethostid = $CFG->mnet_localhost_id; // always local user
        $usernew->confirmed  = 1;
        $usernew->timecreated = time();
        $usernew->password = hash_internal_user_password($testaccountdata->testaccountpwd);
        $usernew->email = $testaccountdata->testaccountemail;
        
        $testusername = testaccount_automation_generateusername($courseadmin);
        
        //call standard _user_create_user function
        //$usernew->id = user_create_user($usernew, false, false);
        $i++;
    }
    //save test account details within mdl_testaccounts table
    
    return 'user created';
}

//function testaccount_automation_generateusername($baseusername, $numtestaccounts){
function testaccount_automation_generateusername($courseadmin){
    global $DB;
    /*$testusername = array();
    $i=1;
    while($i <= $numtestaccounts){
       $testusername[] = $baseusername.'_s'.$i;
       $i++;
    }
    return $testusername;*/
    //get list of test useraccounts associated with this course admin
    $testuseraccounts = $DB->get_records('testaccounts', array('courseadminid' => $courseadmin->id));
    $newtestusername = $courseadmin->username.'_s';
}