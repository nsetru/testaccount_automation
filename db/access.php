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

    'local/testaccount_automation:create' => array( // works in CONTEXT_SYSTEM only

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),

);

