<?php

/**
 * @package    mod_htmltable
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/htmltable/backup/moodle2/backup_htmltable_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the htmltable instance
 */
class backup_htmltable_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the htmltable.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_htmltable_activity_structure_step('htmltable_structure', 'htmltable.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     *
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of htmltables
        $search = "/(" . $base . "\/mod\/htmltable\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@TABLEINDEX*$2@$', $content);

        // Link to htmltable view by moduleid
        $search = "/(" . $base . "\/mod\/htmltable\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@TABLEVIEWBYID*$2@$', $content);

        return $content;
    }
}
