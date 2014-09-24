<?php

/**
 * library functions
 *
 * @package    local
 * @subpackage testaccount_automation
 * @copyright  2014 UCL
 * @license    http://www.ucl.ac.uk
 */

require_once ("$CFG->dirroot/user/lib.php");

/**
 * 
 * @param type $testaccountdata
 * @param type $courseadmin
 * @return string
 */
function testaccount_automation_processuserdata($testaccountdata, $courseadmin){
    $testuserscreated = array();
    
    $numtestaccounts = $testaccountdata->numtestaccounts;
    //validate test accounts before saving
   
    //create user within mdl_user table
    $i=1;
    while($i <= $numtestaccounts){
        
        $testusername = testaccount_automation_generateusername($courseadmin, $i);
        
        if(!empty($testusername)){
            $testaccountdata->username = $testusername;
            $testuseraccountid = testaccount_automation_createtestuser($testaccountdata, $courseadmin);
        }   
        
        //populate array with created test user accounts
        if(!empty($testuseraccountid)){
            $testuserscreated[$testuseraccountid] = $testaccountdata->username;
        }
        
        $i++;
    }
    
    return $testuserscreated;
}

/**
 * 
 * @global type $DB
 * @param type $courseadmin
 * @param type $count
 * @return string
 */
function testaccount_automation_generateusername($courseadmin, $count){
    global $DB;
   
    //get list of test useraccounts associated with this course admin
    $testuseraccounts = $DB->get_records('testaccounts', array('courseadminid' => $courseadmin->id), 'testaccountid DESC');
    if($testuseraccounts){
        $newtestusername = testaccount_automation_createusername($testuseraccounts, $courseadmin);
        
        //check if new username generated already exists within mdl_user table
        $usernameexists = $DB->get_record('user', array('username' => $newtestusername));
        if(!$usernameexists){
            return $newtestusername;
        }
    }else{
        $newtestusername = $courseadmin->username.'_s'.$count;
        return $newtestusername;
    }
        
}

/**
 * 
 * @global type $DB
 * @param type $testuseraccounts
 * @param type $courseadmin
 * @return string
 */
function testaccount_automation_createusername($testuseraccounts, $courseadmin){
    global $DB;
    
    $testuseraccount = current($testuseraccounts);
    $username = $DB->get_field('user', 'username', array('id' => $testuseraccount->testaccountid));
    $usernametmp = explode('_', $username);
    $count = substr($usernametmp[1], 1);
    $count = $count+1;
    if(!empty($count)){
        $testusername = $courseadmin->username.'_s'.$count;
    }
    return $testusername;
}

/**
 * 
 * @global type $CFG
 * @global type $DB
 * @param type $testaccountdata
 * @param type $courseadmin
 * @return boolean
 */
function testaccount_automation_createtestuser($testaccountdata, $courseadmin){
    global $CFG, $DB;
       
    //set few default values to create new user
    $usernew->auth = 'manual';
    $usernew->deleted = 0;
    $usernew->mnethostid = $CFG->mnet_localhost_id; // always local user
    $usernew->confirmed  = 1;
    $usernew->timecreated = time();
    $usernew->password = hash_internal_user_password($testaccountdata->testaccountpwd);
    $usernew->email = $testaccountdata->testaccountemail;
    $usernew->firstname = $testaccountdata->username;
    $usernew->lastname = 'testaccount';
    $usernew->username = $testaccountdata->username;
            
    //call standard _user_create_user function
    $usernew->id = user_create_user($usernew, false, false);
        
    //insert details of test account into mdl_testaccounts table
    if($usernew->id){
        
        $testaccount = (object)$testaccount;
        $testaccount->courseadminid = $courseadmin->id;
        $testaccount->testaccountid = $usernew->id;
        $testaccount->active = 1;
        $testaccount->datecreated = time();
        $testaccount->days = $testaccountdata->numofdays;
        $result = $DB->insert_record('testaccounts', $testaccount);
    }
    
    if($result){
        return $usernew->id;
    }else{
        return false;
    }
}

