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
/*function testaccount_automation_processuserdata($testaccountdata, $courseadmin){
    
    $testuserscreated = array(); 
    $numtestaccounts = $testaccountdata->numtestaccounts;
    //validate test accounts before saving?
   
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
        }else{
            $testuserscreated['error'] = 'Could not create test user account - $testaccountdata->username';
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
/*function testaccount_automation_generateusername($courseadmin, $count){
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
/*function testaccount_automation_createusername($testuseraccounts, $courseadmin){
    global $DB;
    
    /**
    $testuseraccount = current($testuseraccounts);
    $username = $DB->get_field('user', 'username', array('id' => $testuseraccount->testaccountid));
    $usernametmp = explode('_', $username);
    $count = substr($usernametmp[1], 1);
    $count = $count+1;
    if(!empty($count)){
        $testusername = $courseadmin->username.'_s'.$count;
    }
     * 
     */
    /*$lastid = $DB->get_field_sql("SELECT MAX(id) FROM {testaccounts}");
    $lastid = $lastid + 1;
    $testusername = $courseadmin->username.'_s'.$lastid;
    
    return $testusername;
}*/

/**
 * 
 * @global type $CFG
 * @global type $DB
 * @param type $testaccountdata
 * @param type $courseadmin
 * @return boolean
 */
/*function testaccount_automation_createtestuser($testaccountdata, $courseadmin){
    global $CFG, $DB;
    static $counter = 0;
       
    //set few values to create new user
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
            
    //call standard user_create_user() moodle function to create users
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
}*/

/**
 * 
 * @global type $DB
 * @param type $users
 */
function testaccount_automation_printtable($users){
    global $DB;
    
    $table = new html_table();
    $table->width = "95%";
    $table->head = array('username', 'fullname', 'email');
    $columns = array('username','fullname', 'email');
    
    $namefields = get_all_user_name_fields(true);
    foreach($users as $key => $value) {
        $user = $DB->get_record('user', array('id' => $key), 'id, ' . $namefields . ', username, email');
        $user->fullname = fullname($user, true);
        $table->data[] = array (
            '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'">'.$user->username.'</a>',
            $user->fullname,
            $user->email
        );
    }
    return $table;
}

/**
 * 
 * @global type $CFG
 * @global type $PAGE
 * @param type $settingsnav
 * @param type $context
 * @return type
 */
function local_testaccount_automation_extends_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE;
 
    // Only add this settings item on non-site course pages.
    if (!$PAGE->course or $PAGE->course->id == 1) {
        return;
    }
 
    // Only let users with the appropriate capability see this settings item.
    if (!has_capability('moodle/backup:backupcourse', context_course::instance($PAGE->course->id))) {
        return;
    }
 
    if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
        $strfoo = get_string('createtestaccounts', 'local_testaccount_automation');
        $url = new moodle_url('/local/testaccount_automation/index.php', array('course' => $PAGE->course->id));
        $foonode = navigation_node::create(
            $strfoo,
            $url,
            navigation_node::NODETYPE_LEAF,
            'testaccount_automation',
            'testaccount_automation',
            new pix_icon('t/addcontact', $strfoo)
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $foonode->make_active();
        }
        $settingnode->add_node($foonode);
    }
}