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
 * @package   tool_enrolexport_tci
 * @copyright 2019 Adam Yarris
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
class mapping_edit_form extends moodleform {

    /**
     * Link ID.
     * @var int
     */
    protected $id;

    /**
     * The program code that we don't set.
     * @var int
     */
    protected $programcode = '';

    /**
     * A comma-separated list of course to be attached to the program code?
     * @var string
     */
    protected $course = '';

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
        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'editexportheader', get_string('fieldmappings', 'enrolexporter_tci'));

        // Program code.
        $mform->addElement('text', 'programcode', get_string('programcode', 'enrolexporter_tci'), array('size' => 60));
        $mform->setType('programcode', PARAM_RAW);
        $mform->addRule('programcode', null, 'required');
        $mform->addRule('programcode', null, 'maxlength', 64);

        // Course.
        $options = array('multiple' => true, 'includefrontpage' => true);
        $mform->addElement('course', 'course', get_string('course'), $options);
        $mform->setType('course', PARAM_TEXT);
        $mform->addRule('course', null, 'required');

        // Hidden.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true);
    }

}