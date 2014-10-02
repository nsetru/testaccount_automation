<?php

/**
 * Handle Capabilities
 * 
 * @package local
 * @subpackage testaccount_automation
 * @copyright  2014 UCL
 * @license    http://www.ucl.ac.uk
 * 
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'local/testaccount_automation:create' => array( // works in CONTEXT_COURSE only

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

);

