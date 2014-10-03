<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Version information
 *
 * @package    local
 * @subpackage testaccount_automation
 * @copyright  2014 UCL
 * @license    http://www.ucl.ac.uk
 */

$string['pluginname'] = 'Test accounts automation';
$string['createtestaccounts'] = 'Create Test User Accounts';
$string['outputheading'] = 'Request Moodle student test accounts';

//form 
$string['numtestaccounts'] = 'Number of Test Accounts';
$string['numtestaccounts_help'] = 'Your test accounts will be automatically enrolled into this course. '
        . 'They will be active for the number of days you request and during this time can be also be enrolled onto other courses where student testing is required. '
        . 'You may have a maximum of 15 test accounts at any given time. The accounts are ‘Moodle only’ accounts and will not give access to any other UCL services e.g Lecturecast, Library Journals etc.';
$string['numofdays'] = 'Number of days';
$string['numofdays_help'] = 'Your test accounts will be active for the number of days you select, at the end of this period the accounts '
        . 'and all student data associated with them (forum postings, assignment uploads etc.) will be deleted from Moodle.';
$string['testaccountpwd'] = 'Password for Test Accounts';
$string['testaccountpwd_help'] = "Please make a note of your chosen password before clicking 'Create my test accounts' below - 
    if you fail to do this and forget the password you will have to wait until the accounts expire before you can apply for more.
    (can we change the ‘Save’ button to ‘Create my test accounts’  ?  if not change above text to ‘Save’ where appropriate)";
$string['testaccountemail'] = 'Email for Test Accounts';
$string['testaccountemail_help'] = 'All Moodle communications to your test students will be sent to the address shown. '
        . 'If you prefer, you can change the default address to another mailbox that you have access to.';
$string['successnotification'] = 'Test-user accounts created for user : {$a}';
$string['limitexceednotification'] = '<p>Sorry! You have created <b>{$a->count}</b> test-user accounts.</p> '
        . '<p>Username : {$a->username} allowed to create maximum of 15 test-user accounts. You have exceeded the limit.</p> '
        . '<p>Please re-use existing test-user accounts</p>';
$string['limitexceedmessage'] = 'Username:{$a->username} created {$a->count} test-users accounts. Max limit:{$a->maxlimit}.';
$string['pwdplaintext'] = '<p>Password for above test-user accounts  :  <b>{$a}</b> </p> '
        . '<p>Note:Please note down this password and keep it safe. The only way to retrieve password is by asking ucl support to reset password</p>';

//capability
$string['testaccount_automation:create'] = 'Create Test User Accounts';

//email
$string['emailsubject'] = 'Test-user account details';

