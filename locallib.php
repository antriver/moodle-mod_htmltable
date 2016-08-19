<?php

/**
 * Private htmltable module utility functions
 *
 * @package    mod_htmltable
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/htmltable/lib.php");

/**
 * File browsing support class
 */
class htmltable_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }

    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

function htmltable_get_editor_options($context) {
    global $CFG;
    return array('subdirs'      => 1,
                 'maxbytes'     => $CFG->maxbytes,
                 'maxfiles'     => -1,
                 'changeformat' => 1,
                 'context'      => $context,
                 'noclean'      => 1,
                 'trusttext'    => 0
    );
}
