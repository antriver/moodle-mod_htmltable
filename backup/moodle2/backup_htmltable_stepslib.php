<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
        // Define each element separated.
        $htmltable = new backup_nested_element(
            'htmltable',
            array('id'),
            array(
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
            )
        );

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $htmltable->set_source_table('htmltable', array('id' => backup::VAR_ACTIVITYID));

        // Define file annotations
        $htmltable->annotate_files('mod_htmltable', 'intro', null); // This file areas haven't itemid.
        $htmltable->annotate_files('mod_htmltable', 'content', null); // This file areas haven't itemid.

        // Return the root element (htmltable), wrapped into standard activity structure.
        return $this->prepare_activity_structure($htmltable);
    }
}
