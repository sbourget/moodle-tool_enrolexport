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
 * @package   enrolexporter_oneroster11
 * @copyright 2019 Adam Yarris
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

/**
 * This defines the edit form for managing the exports.
 *
 * @package   enrolexporter_oneroster11
 * @copyright 2019 Adam Yarris
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mapping_edit_form_oneroster11 extends moodleform {

    /**
     * Link ID.
     * @var int
     */
    protected $id;

    /**
     * A comma-separated list of course to be attached to the session?
     * @var string
     */
    protected $course = '';

    /**
     * The Unique ID for each academic session, from academicSessions.csv
     * @var string
     */
    protected $academicsessions_sourcedid = '';

    /**
     * The name of the academic session, from academicSessions.csv
     * @var string
     */
    protected $academicsessions_title = '';

    /**
     * The type of academic session, must be one of the following:
     * - term
     * - gradingPeriod
     * - schoolYear
     * - semester
     * Found in academicSessions.csv
     * @var string
     */
    protected $academicsessions_type = '';

    /**
     * Constructor.
     * @param string $actionurl
     * @param int $id
     */
    public function __construct($actionurl, $id) {
        $this->id = $id;
        parent::__construct($actionurl);
    }

    /**F
     * Form definition.
     */
    public function definition() {
        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'editexportheader', get_string('fieldmappings', 'enrolexporter_oneroster11'));

        // Course.
        $options = array('multiple' => true, 'includefrontpage' => true);
        $mform->addElement('course', 'course', get_string('course'), $options);
        $mform->setType('course', PARAM_TEXT);
        $mform->addRule('course', null, 'required');

        $mform->addElement('header', 'academicsessions_header', get_string('academicsessions_header', 'enrolexporter_oneroster11'));

        $mform->addElement('select', 'academicsessions_sourcedid', get_string('academicsessions_sourcedId', 'enrolexporter_oneroster11'), array(
            'fy' => 'Full Year',
            's1' => 'Semester 1',
            's2' => 'Semester 2'
        ), array('multiple' => false));
        $mform->setType('academicsessions_sourcedid', PARAM_TEXT);

        $mform->addElement('text', 'academicsessions_title', get_string('academicsessions_title', 'enrolexporter_oneroster11'));
        $mform->setType('academicsessions_title', PARAM_ALPHANUMEXT);

        $mform->addElement('select', 'academicsessions_type', get_string('academicsessions_type', 'enrolexporter_oneroster11'), array(
            'term' => 'term',
            'gradingPeriod' => 'gradingPeriod',
            'schoolYear' => 'schoolYear',
            'semester' => 'semester'
        ), array('multiple' => false));
        $mform->setType('academicsessions_type', PARAM_TEXT);

        // Hidden.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true);
    }

}