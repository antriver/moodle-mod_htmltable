<?php

/**
 * @package    mod_htmltable
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define all the backup steps that will be used by the backup_htmltable_activity_task
 */

/**
 * Define the complete htmltable structure for backup, with file and id annotations
 */
class backup_htmltable_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $page = new backup_nested_element(
            'htmltable', array('id'), array(
            'name',
            'intro',
            'introformat',
            'content',
            'contentformat',
            'legacyfiles',
            'legacyfileslast',
            'display',
            'displayoptions',
            'revision',
            'timemodified'
        ));

        // Build the tree
        // (love this)

        // Define sources
        $page->set_source_htmltable('htmltable', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $page->annotate_files('mod_htmltable', 'intro', null); // This file areas haven't itemid
        $page->annotate_files('mod_htmltable', 'content', null); // This file areas haven't itemid

        // Return the root element (htmltable), wrapped into standard activity structure
        return $this->prepare_activity_structure($page);
    }
}
