<?php

/**
 * @package    mod_htmltable
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_htmltable_activity_task
 */

/**
 * Structure step to restore one htmltable activity
 */
class restore_htmltable_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('htmltable', '/activity/htmltable');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_htmltable($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the htmltable record
        $newitemid = $DB->insert_record('htmltable', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add htmltable related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_htmltable', 'intro', null);
        $this->add_related_files('mod_htmltable', 'content', null);
    }
}
