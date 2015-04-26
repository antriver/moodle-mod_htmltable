<?php

/**
 * List of all htmltables in course
 *
 * @package    mod_htmltable
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

add_to_log($course->id, 'htmltable', 'view all', "index.php?id=$course->id", '');

$strpage         = get_string('modulename', 'htmltable');
$strpages        = get_string('modulenameplural', 'htmltable');
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/htmltable/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strpages);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strpages);
echo $OUTPUT->header();

if (!$pages = get_all_instances_in_course('htmltable', $course)) {
    notice(get_string('thereareno', 'moodle', $strpages), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$htmltable = new html_htmltable();
$htmltable->attributes['class'] = 'generalhtmltable mod_index';

if ($usesections) {
    $htmltable->head  = array ($strsectionname, $strname, $strintro);
    $htmltable->align = array ('center', 'left', 'left');
} else {
    $htmltable->head  = array ($strlastmodified, $strname, $strintro);
    $htmltable->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($pages as $page) {
    $cm = $modinfo->cms[$page->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($page->section !== $currentsection) {
            if ($page->section) {
                $printsection = get_section_name($course, $page->section);
            }
            if ($currentsection !== '') {
                $htmltable->data[] = 'hr';
            }
            $currentsection = $page->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($page->timemodified)."</span>";
    }

    $class = $page->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed

    $htmltable->data[] = array (
        $printsection,
        "<a $class href=\"view.php?id=$cm->id\">".format_string($page->name)."</a>",
        format_module_intro('htmltable', $page, $cm->id));
}

echo html_writer::htmltable($htmltable);

echo $OUTPUT->footer();
