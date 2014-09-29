<?php


require_once ("$CFG->dirroot/user/lib.php");

/**
 * Description of testaccount_automation_create
 *
 * @author cceanse
 */
class testaccount_automation_create {
    //define constants
    const MAX_ACCOUNTS_LIMIT   =   15; //max test-user accounts a user can create
    
    /**
     * 
     * @global type $DB
     * @param stdClass $courseadmin
     * @return boolean
     */
    public function testaccount_automation_checklimitexceeds(stdClass $courseadmin){
        global $DB;
        
        $testaccountscount = $DB->count_records('testaccounts', array('courseadminid' => $courseadmin->id, 'active' => 1));
        if($testaccountscount >= self::MAX_ACCOUNTS_LIMIT){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 
     * @param type $testaccountdata
     * @param type $courseadmin
     * @return string
     */
    public function testaccount_automation_processuserdata($formdata, stdClass $courseadmin){
        
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
        
        //send email to course-admin details about test-user accounts
        $this->testaccount_automation_sendemail($testuserscreated, $courseadmin);
        return $testuserscreated;
    }
    
    /**
     * 
     * @global type $DB
     * @param type $courseadmin
     * @return string
     */
    private function testaccount_automation_generateusername(stdClass $courseadmin){
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
    private function testaccount_automation_createtestuser($testaccountdata, stdClass $courseadmin){
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
            $testaccount->datecreated = $usernew->timecreated;
            $testaccount->days = $testaccountdata->numofdays;
            $testaccount->dateexpired = $this->testaccount_automation_getexpirydate($testaccount->datecreated, $testaccount->days);
            $result = $DB->insert_record('testaccounts', $testaccount);
        }

        if ($result) {
            return $usernew->id;
        } else {
            return false;
        }
    }
    
    private function testaccount_automation_getexpirydate($datecreated, $days){
        if(!empty($datecreated) && !empty($days)){
            $dateexpired = strtotime('+'.$days.' days', $datecreated);
            return $dateexpired;
        }
        
        return false;
    }
    
    /**
     * 
     * @global type $DB
     * @param type $testusers
     * @param stdClass $courseadmin
     */
    private function testaccount_automation_sendemail($testusers, stdClass $courseadmin){
        global $DB;
        //get course admin details
        $touser = $DB->get_record('user', array('id' => $courseadmin->id));
        $testuserscount = count($testusers);

        //email meesage body
        $message = "<head> You have created $testuserscount test-user accounts. These test-user accounts are owned by Username:$courseadmin->username . </head>"
                . "<br /><br />"
                . "<body id=\"email\"><table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"95%\">";
        $message .= "<tr>"
                . "<th>user</th>"
                . "<th>fullname</th>"
                . "<th>email</th>"
                . "<th>Created date</th>"
                . "<th>Expiry date</th>"
                . "</tr>";
        
        $namefields = get_all_user_name_fields(true);
        foreach ($testusers as $key => $value) {
            $testuser = $DB->get_record('user', array('id' => $key), 'id, ' . $namefields . ', username, email');
            $testuserdetail = $DB->get_record('testaccounts', array('testaccountid' => $key));
            $testuser->fullname = fullname($testuser, true);
            $createddate = date('d-m-Y H:i:s', $testuserdetail->datecreated);
            $expirydate = date('d-m-Y H:i:s', $testuserdetail->dateexpired);
            $message .= '<tr><td>'.$testuser->username.'</td>'
                    . '<td>'.$testuser->fullname.'</td>'
                    . '<td>'.$testuser->email.'</td>'
                    . '<td>'.$createddate.'</td>'
                    . '<td>'.$expirydate.'</td>'
                    . "</tr>";
        }
        $message .= "</table><br />";
        
        $message .= "<p><b>Please note: These test-user accounts will be deleted after expiry date and can no longer be accessed.</b></p>"
                . "</body>";
        
        $messagehtml = $message;
        $messagetext = html_to_text($message);
        

        //email subject
        $subject = get_string('emailsubject', 'local_testaccount_automation', $testuserscount);
 
        //from email-address
        $supportuser = core_user::get_support_user();
        
        if ($message) {
            // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
            email_to_user($touser, $supportuser, $subject, $messagetext, $messagehtml);
        }
    }

}
