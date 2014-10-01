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
$string['outputheading'] = 'Details for Test User Accounts';

//form 
$string['numtestaccounts'] = 'Number of Test Accounts';
$string['numtestaccounts_help'] = 'Number of test-user accounts the user could create in this request.';
$string['numofdays'] = 'Number of days';
$string['numofdays_help'] = 'Number of days test-user accounts are requested. After days are expired the test-user accounts are deleted.';
$string['testaccountpwd'] = 'Password for Test Accounts';
$string['testaccountpwd_help'] = 'Password to access test-user accounts.';
$string['testaccountemail'] = 'Email for Test Accounts';
$string['testaccountemail_help'] = 'Email address for test-user accounts';
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

