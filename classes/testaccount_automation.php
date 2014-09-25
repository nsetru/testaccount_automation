<?php


require_once ("$CFG->dirroot/user/lib.php");

/**
 * Description of testaccount_automation
 *
 * @author cceanse
 */
class testaccount_automation {
    //define constants
    
    /**
     * 
     * @param type $testaccountdata
     * @param type $courseadmin
     * @return string
     */
    public function testaccount_automation_processuserdata($formdata, $courseadmin){
        
        $testuserscreated = array();
        $numtestaccounts = $formdata->numtestaccounts;
        //validate test accounts before saving?
        //create user within mdl_user table
        $i = 1;
        while ($i <= $numtestaccounts) {

            $testusername = $this->testaccount_automation_generateusername($courseadmin);
            
            if (!empty($testusername)) {
                $formdata->username = $testusername;
                $testuseraccountid = $this->testaccount_automation_createtestuser($formdata, $courseadmin);
            }

            //populate array with created test user accounts
            if (!empty($testuseraccountid)) {
                $testuserscreated[$testuseraccountid] = $formdata->username;
            } else {
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
     * @return string
     */
    private function testaccount_automation_generateusername($courseadmin){
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
        $lastid = $DB->get_field_sql("SELECT MAX(id) FROM {testaccounts}");
        $lastid = $lastid + 1;
        $testusername = $courseadmin->username . '_s' . $lastid;
        
        //check if new username generated already exists within mdl_user table
        $usernameexists = $DB->get_record('user', array('username' => $testusername));
        if($usernameexists){
            return ;
        }
        
        return $testusername;
    }
    
    /**
     * 
     * @global type $CFG
     * @global type $DB
     * @staticvar int $counter
     * @param type $testuseraccounts
     * @param type $courseadmin
     * @return boolean
     */
    private function testaccount_automation_createtestuser($testaccountdata, $courseadmin){
        global $CFG, $DB;

        //set few values to create new user
        $usernew->auth = 'manual';
        $usernew->deleted = 0;
        $usernew->mnethostid = $CFG->mnet_localhost_id; // always local user
        $usernew->confirmed = 1;
        $usernew->timecreated = time();
        $usernew->password = hash_internal_user_password($testaccountdata->testaccountpwd);
        $usernew->email = $testaccountdata->testaccountemail;
        $usernew->firstname = $testaccountdata->username;
        $usernew->lastname = 'testaccount';
        $usernew->username = $testaccountdata->username;

        //call standard user_create_user() moodle function to create users
        $usernew->id = user_create_user($usernew, false, false);

        //insert details of test account into mdl_testaccounts table
        if ($usernew->id) {

            $testaccount = (object) $testaccount;
            $testaccount->courseadminid = $courseadmin->id;
            $testaccount->testaccountid = $usernew->id;
            $testaccount->active = 1;
            $testaccount->datecreated = time();
            $testaccount->days = $testaccountdata->numofdays;
            $result = $DB->insert_record('testaccounts', $testaccount);
        }

        if ($result) {
            return $usernew->id;
        } else {
            return false;
        }
    }
    
}
