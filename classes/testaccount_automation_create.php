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
            return $testaccountscount;
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

            if (!empty($testuseraccountid)) {
                //enrol users
                $enrolresult = $this->testaccount_automation_enroluser($testuseraccountid, $formdata->course, $courseadmin);
                if($enrolresult != 'true'){
                    $testuserscreated['enrol_error'] = $enrolresult;
                }
                
                //populate array with created test user accounts
                $testuserscreated[$testuseraccountid] = $formdata->username; 
            } else {
                $testuserscreated['user_error'] = 'Could not create test user account - $testaccountdata->username';
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
     * @param type $userid
     * @param type $courseid
     */
    private function testaccount_automation_enroluser($userid, $courseid, $courseadmin){
        global $CFG, $DB;
        
        //get course details
        $course = $DB->get_record('course', array('id' => $courseid));
        
        $context = context_course::instance($course->id, MUST_EXIST);

        //get roleid of a student
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));

        //get duration on the manual plugin for that course
        $enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
        
        //get current user roles(roleid, name)
        $userroles = get_user_roles($context, $courseadmin->id, false);
        foreach($userroles as $userrole){
            $rolename = $DB->get_field('role', 'name', array('id' => $userrole->roleid));
            $currentuserroles[$userrole->roleid] = $rolename;
        }
        
        require_once($CFG->libdir . '/enrollib.php');
        
        // Rollback all enrolment if an error occurs
        // (except if the DB doesn't support it).
        // Retrieve the manual enrolment plugin.
        $transaction = $DB->start_delegated_transaction(); 
        
        $enrol = enrol_get_plugin('manual');
        
        if (empty($enrol)) {
            //throw new moodle_exception('manualpluginnotinstalled', 'enrol_manual');
            $errormsg = get_string('manualpluginnotinstalled', 'enrol_manual');
            return $errormsg;
        }

        // Check that the user has the permission to manual enrol.
        require_capability('enrol/manual:enrol', $context);

        // Throw an exception if user is not able to assign the role.
        //TODO::make this work
        $roles = get_assignable_roles($context);
        /*if (!array_key_exists($currentuserroles, $roles)) {
            $errorparams = new stdClass();
            $errorparams->roleid = $studentroleid;
            $errorparams->courseid = $course->id;
            $errorparams->userid = $userid;
            //throw new moodle_exception('wsusercannotassign', 'enrol_manual', '', $errorparams);
            $errormsg = get_string('wsusercannotassign', 'enrol_manual', $errorparams);
            return $errormsg;
        }*/

        // Check manual enrolment plugin instance is enabled/exist.
        $instance = null;
        $enrolinstances = enrol_get_instances($course->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {

            if ($courseenrolinstance->enrol == "manual") {
                $instance = $courseenrolinstance;
                break;
            }
        }
        if (empty($instance)) {
            $errorparams = new stdClass();
            $errorparams->courseid = $course->id;
            //throw new moodle_exception('wsnoinstance', 'enrol_manual', $errorparams);
            $errormsg = get_string('wsnoinstance', 'enrol_manual', $errorparams);
            return $errormsg;
        }

        // Check that the plugin accept enrolment (it should always the case, it's hard coded in the plugin).
        if (!$enrol->allow_enrol($instance)) {
            $errorparams = new stdClass();
            $errorparams->roleid = $studentroleid;
            $errorparams->courseid = $course->id;
            $errorparams->userid = $userid;
            //throw new moodle_exception('wscannotenrol', 'enrol_manual', '', $errorparams);
            $errormsg = get_string('wscannotenrol', 'enrol_manual', $errorparams);
            return $errormsg;
        }

         //calculate timestart 
        $today = time();
        $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
        $timestart = $today;

        //calculate timeend
        if ($enrolperiod <= 0) {
            $timeend = 0;
        } else {
            $timeend = $timestart + ($enrol->enrolperiod * 24 * 60 * 60);
        }

        $enrol->enrol_user($instance, $userid, $studentroleid, $timestart, $timeend);


        $transaction->allow_commit();
        
        return 'true';
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
        $message = "<head> Dear $touser->firstname $touser->lastname <br /><br />You have created $testuserscount accounts.  </head>"
                . "<br /><br />"
                . "<body id=\"email\">"
                . "<p>Each account is associated with your your username : $courseadmin->username. You are responsible for how they are used. It's important that you adhere to the following:</p>"
                . "<li> Do not upgrade the accounts to Tutor or Course administrator roles within Moodle.</li>"
                . "<li> Do not share the accounts with colleagues - they are able to create their own test accounts if they need to.</li>"
                . "<p><i>Note that these are ‘Moodle only’ accounts and will not give access to any other UCL services e.g Lecturecast, Library Journals etc.</i><p>"
                . "<p>More Information about how these accounts can be used may be found <a href=\"https://wiki.ucl.ac.uk/display/MoodleResourceCentre/Student+Test+Accounts+for+Moodle\">here</a></p>"
                . "<br /><br />";
        $message .= "<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\" width=\"95%\">"
                . "<tr>"
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
        
        $message .= "</body>";
        
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
