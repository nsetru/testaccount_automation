<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * settings
 *
 * @package    local
 * @subpackage testaccount_automation
 * @copyright  2014 UCL
 * @license    http://www.ucl.ac.uk
 */

defined('MOODLE_INTERNAL') || die;

global $USER;
$capabilities = array(
    'moodle/backup:backupcourse',
    'moodle/category:manage',
    'moodle/course:create',
    'moodle/site:approvecourse',
    'moodle/course:update'
);

$usercontext = context_user::instance($USER->id);;
if($hassiteconfig or has_any_capability($capabilities, $usercontext)){
    $ADMIN->add('root', new admin_category('local', 'UCL Tools'));
    /*$ADMIN->add('local', 
        new admin_externalpage('testaccount_automation', get_string('pluginname', 'local_testaccount_automation'), 
            $CFG->wwwroot . '/local/testaccount_automation/index.php', $capabilities));*/
    $ADMIN->add('local', 
        new admin_externalpage('testaccount_automation', get_string('pluginname', 'local_testaccount_automation'), 
            $CFG->wwwroot . '/local/testaccount_automation/index.php', $capabilities));
}
