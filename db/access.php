<?php

/**
 * @package    mod_htmltable
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$capabilities = array(
    'mod/htmltable:view'        => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'guest' => CAP_ALLOW,
            'user'  => CAP_ALLOW,
        )
    ),
    'mod/htmltable:addinstance' => array(
        'riskbitmask'          => RISK_XSS,
        'captype'              => 'write',
        'contextlevel'         => CONTEXT_COURSE,
        'archetypes'           => array(
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),

);
