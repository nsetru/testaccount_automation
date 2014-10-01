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

function testaccount_automation_printalltestusers($courseadmin){
    global $DB;
    
    $table = new html_table();
    $table->width = "95%";
    $table->head = array('username', 'fullname', 'email', 'created date', 'expiry date');
    $columns = array('username','fullname', 'email', 'created date', 'expiry date');
    
    //get all test-users created by courseadmin
    $testusers = $DB->get_records('testaccounts', array('courseadminid' => $courseadmin->id, 'active' => 1));
    $namefields = get_all_user_name_fields(true);
    foreach($testusers as $testuser){
        //get other details of testuser  from mdl_user table
        $userdetail = $DB->get_record('user', array('id' => $testuser->id));
        $testuser->fullname = fullname($userdetail, true);
        $datecreated = date('d-m-Y', $testuser->datecreated);
        $dateexpiry = date('d-m-Y', $testuser->dateexpired);
        $table->data[] = array (
            '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$testuser->id.'&amp;course='.SITEID.'">'.$userdetail->username.'</a>',
            $testuser->fullname,
            $userdetail->email,
            $datecreated,
            $dateexpiry
        );
    }
    
    return $table;
}

/**
 * function to extend
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


/**
 * Cron to delete expired test-user accounts
 * 
 * set cron job function 
 * @global type $DB
 */
function local_testaccount_automation_cron(){
    global $DB;
    
    mtrace('Delete expired test-user accounts.. ');
    // get expiry date of all test users
    $currenttime = time();
    $sql = 'SELECT * from {testaccounts} where dateexpired < :currenttime';
    $expiredtestusers = $DB->get_records_sql($sql, array('currenttime' => $currenttime));
    // get test-user accounts that have passed expired date
    if(!empty($expiredtestusers)){
        foreach($expiredtestusers as $testuser){
            $user = $DB->get_record('user', array('id' => $testuser->testaccountid));
            
            try{
                // delete user from mdl_user table and un-enrol from activities and courses by calling standard delete_user() moodle core funcion 
                mtrace("Deleting $user->username test-user account..");
                $deleted = delete_user($user);
                //update 'active' field in mdl_testaccounts table to '0'
                if ($deleted) {
                    $updaterecord = new stdClass();
                    $updaterecord->id = $testuser->id;
                    $updaterecord->active = 0;
                    $DB->update_record('testaccounts', $updaterecord);
                }
            } catch (Exception $ex) {
                mtrace('Error deleting '.$user->username.' test-user account: ' . $ex->getMessage());
            }
                        
            $expirydate = date('d-m-Y', $testuser->dateexpired);
            mtrace("Username : $user->username deleted. Test-user account expired on:$expirydate.. ");
            
        }
    }
    
}