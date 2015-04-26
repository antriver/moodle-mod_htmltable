<?php

/**
 * htmltable add/edit instance form
 *
 * @package    mod_htmltable
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/htmltable/locallib.php');
require_once($CFG->libdir.'/filelib.php');

class mod_htmltable_mod_form extends moodleform_mod {

    function definition()
    {
        global $CFG, $DB, $PAGE;

        $PAGE->requires->jquery();

        $mform = $this->_form;

        $config = get_config('htmltable');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->add_intro_editor($config->requiremodintro);

        //-------------------------------------------------------
        $mform->addElement('header', 'contentsection', get_string('contentheader', 'htmltable'));
        $mform->setExpanded('contentsection');

        // Editing an existing table?
        if ($this->current && property_exists($this->current, 'content') && !is_null($this->current->content)) {
        	// The previous data is saved in JSON format so set it as a JS variable
        	$tableHTML = '<script> var currentTable = ' . $this->current->content . '; </script>';
        } else {
            $tableHTML = '<script> var currentTable; </script>';
        }

        // Table header row (with add column button)
        $tableHTML .= <<<EOT
        	<table class="userinfotable table table-striped" id="htmltable_edittable">
        		<tr class="addrow"><td style="text-align:center;"><a href="#" class="htmltable_addrow_button"><button><i class="icon-plus"></i> Add A Row</button></a></td></tr>
        	</table>
        	<script src="/mod/htmltable/js/htmltable.js"></script>
EOT;

	 	$mform->addElement('hidden', 'content' , '');
        $mform->setType('content', PARAM_RAW);

	 	$mform->addElement('hidden', 'contentformat' , '1');
        $mform->setType('contentformat', PARAM_RAW);

        $mform->addElement('html',$tableHTML);

        $mform->addElement('html','
        <br/>
        <div class="generalbox inset">

            <h4 class="advice">
                <i class="fa fa-lightbulb-o"></i> Tip: You can style your text like this:
            </h4>

            <table class="table table-striped">
                <thead>
                	<tr>
                		<th>Style</td>
                		<th>Type This</td>
                		<th>To Get This</td>
                	</tr>
                </thead>
                <tbody>
                	<tr>
                		<td style="width:140px;">Bold</td>
                		<td>**bold**</td>
                		<td style="width:200px;"><strong>bold</strong></td>
                	</tr>
                	<tr>
                		<td>Italic</td>
                		<td>*italic*</td>
                		<td><em>italic</em></td>
                	</tr>
                	<tr>
                		<td>Links (With your own text)</td>
                		<td>[Text you want to appear](http://www.website-you-want-to-link-to.com)</td>
                		<td><a href="http://www.website-you-want-to-link-to.com">Text you want to appear</a></td>
                	</tr>
                	<tr>
                		<td>Links (Showing URL)</td>
                		<td>&lt;http://www.google.com&gt;</td>
                		<td><a href="http://www.google.com">http://www.google.com</a></td>
                	</tr>
                </tbody>
            </table>

        </div>');

        //-------------------------------------------------------
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));

        if ($this->current->instance) {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions), $this->current->display);
        } else {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions));
        }

        //Change display options to user friendly labels
        foreach ( $options as &$option )
        {
        	switch ( $option )
        	{
        		case 'Embed':
        			$option = 'Show on course page';
        		break;

        		case 'Open':
        			$option = 'Click to view';
        		break;
        	}
        }

        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'display', get_string('displayselect', 'htmltable'), $options);
            $mform->setDefault('display', $config->display);
        }

        if (array_key_exists(RESOURCELIB_DISPLAY_POPUP, $options)) {
            $mform->addElement('text', 'popupwidth', get_string('popupwidth', 'htmltable'), array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupwidth', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupwidth', PARAM_INT);
            $mform->setDefault('popupwidth', $config->popupwidth);

            $mform->addElement('text', 'popupheight', get_string('popupheight', 'htmltable'), array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupheight', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupheight', PARAM_INT);
            $mform->setDefault('popupheight', $config->popupheight);
        }

        $mform->addElement('advcheckbox', 'printheading', get_string('printheading', 'htmltable'));
        $mform->setDefault('printheading', $config->printheading);
        $mform->addElement('advcheckbox', 'printintro', get_string('printintro', 'htmltable'));
        $mform->setDefault('printintro', $config->printintro);

        // add legacy files flag only if used
        if (isset($this->current->legacyfiles) and $this->current->legacyfiles != RESOURCELIB_LEGACYFILES_NO) {
            $options = array(RESOURCELIB_LEGACYFILES_DONE   => get_string('legacyfilesdone', 'htmltable'),
                             RESOURCELIB_LEGACYFILES_ACTIVE => get_string('legacyfilesactive', 'htmltable'));
            $mform->addElement('select', 'legacyfiles', get_string('legacyfiles', 'htmltable'), $options);
            $mform->setAdvanced('legacyfiles', 1);
        }

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();

        //-------------------------------------------------------
        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
    }

    function data_preprocessing(&$default_values) {
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('htmltable');
            $default_values['htmltable']['format'] = $default_values['contentformat'];
            $default_values['htmltable']['text']   = file_prepare_draft_area($draftitemid, $this->context->id, 'mod_htmltable', 'content', 0, htmltable_get_editor_options($this->context), $default_values['content']);
            $default_values['htmltable']['itemid'] = $draftitemid;
        }
        if (!empty($default_values['displayoptions'])) {
            $displayoptions = unserialize($default_values['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $default_values['printintro'] = $displayoptions['printintro'];
            }
            if (isset($displayoptions['printheading'])) {
                $default_values['printheading'] = $displayoptions['printheading'];
            }
            if (!empty($displayoptions['popupwidth'])) {
                $default_values['popupwidth'] = $displayoptions['popupwidth'];
            }
            if (!empty($displayoptions['popupheight'])) {
                $default_values['popupheight'] = $displayoptions['popupheight'];
            }
        }
    }
}

