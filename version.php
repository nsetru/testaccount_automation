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

defined('MOODLE_INTERNAL') || die;

$plugin->version = 2014092500;
$plugin->requires = 2013110500;
$plugin->component = 'local_testaccount_automation';
$plugin->cron = 24*60*60; // run cron approximately once in a day
