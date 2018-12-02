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
 * This block generates a simple list of exports based on the users profile.
 *
 * @package   tool_enrolexport
 * @copyright 2013 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

/**
 * This defines the edit form for managing the exports.
 *
 * @package   tool_enrolexport
 * @copyright 2010 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_edit_form extends moodleform {
    /**
     * Link ID.
     * @var int
     */
    protected $id;

    /**
     * Link text to display.
     * @var string
     */
    protected $name = '';

    /**
     * URL of web-export.
     * @var string
     */
    protected $courses = '';

    /**
     * Additional notes to display.
     * @var string
     */
    protected $exporter = '';

    /**
     * Constructor.
     * @param string $actionurl
     * @param int $id
     */
    public function __construct($actionurl, $id) {
        $this->id = $id;
        parent::__construct($actionurl);
    }

    /**
     * Form definition.
     */
    public function definition() {
        global $DB;
        $config = get_config('tool_enrolexport');
        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'editexportheader', get_string('manageexports', 'tool_enrolexport'));

        $mform->addElement('text', 'name', get_string('name', 'tool_enrolexport'), array('size' => 60));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', null, 'maxlength', 250);

        $mform->addElement('text', 'courses', get_string('courses', 'tool_enrolexport'), array('size' => 60));
        $mform->setType('courses', PARAM_TEXT);
        $mform->addRule('courses', null, 'required');
        $mform->addRule('courses', null, 'maxlength', 250);

        $mform->addElement('text', 'exporter', get_string('exporter', 'tool_enrolexport'), array('size' => 60));
        $mform->setType('exporter', PARAM_TEXT);
        $mform->addRule('exporter', null, 'maxlength', 250);

        // Hidden.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true);
    }

}