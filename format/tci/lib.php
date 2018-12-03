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
 * library of functions.
 *
 * @package    enrolexporter_tci
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function enrolexporter_tci_export($settings) {
    $courses = explode(",", $settings->courses);
    $studentlist = array();
    $teacherlist = array();
    foreach ($courses as $courseid) {
        $context = context_course::instance($courseid);
        $teacherlist[$courseid] = get_enrolled_users($context, 'tool/enrolexport:includeinexportasteacher');
        $studentlist[$courseid] = get_enrolled_users($context, 'tool/enrolexport:includeinexportasstudent');
    }
    tci_export_teachers($teacherlist);
}

/**
 * This file builds and exports the teachers.
 * The teacher file needs: Email, Firstname, Lastname, password, password confirm, programcode.
 */
function tci_export_teachers($teacherlist) {
    global $CFG;
    require_once($CFG->libdir . '/csvlib.class.php');

    // 1. Get enrolled users.
    // 2. Extract teachers.
    // 3. Create CSV.

}

/**
 * This file builds and exports the teachers.
 * first_initial, last_name, username, password, password_confirm, teacher_email, program_code, class_period.
 */
function tci_export_students($studentlist) {

}