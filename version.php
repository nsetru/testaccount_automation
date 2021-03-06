<?php
/**
 * Version information
 *
 * @package    local_testaccount_automation
 * @subpackage testaccount_automation
 * @copyright  2014 UCL
 * @license    http://www.ucl.ac.uk
 */

defined('MOODLE_INTERNAL') || die;

$plugin->version = 2014100200;
$plugin->requires = 2013110500;
$plugin->component = 'local_testaccount_automation';
$plugin->cron = 24*60*60; // run cron approximately once in a day
