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

require_once($CFG->libdir .'/spout/src/Spout/Autoloader/autoload.php');

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

function enrolexporter_tci_export($settings) {
    $courses = explode(",", $settings->courses);
    $studentlist = array();
    $teacherlist = array();
    foreach ($courses as $courseid) {
        $context = context_course::instance($courseid);
        $teacherlist[$courseid] = get_enrolled_users($context, 'enrolexporter/tci:includeinexportasteacher');
        $studentlist[$courseid] = get_enrolled_users($context, 'enrolexporter/tci:includeinexportasstudent');
    }
    tci_export_teachers($teacherlist);// test
    tci_export_students($studentlist);
}

/**
 * This file builds and exports the teachers.
 * The teacher file needs: Email, Firstname, Lastname, password, password confirm, programcode.
 */
function tci_export_teachers($teacherlist) {
    // 1. Get enrolled users.
    // 2. Extract teachers.
    // 3. Create CSV.
    $writer = WriterFactory::create(Type::CSV);
    $path = get_config('tool_enrolexport', 'exportpath') . '/tci';

    if (!file_exists($path)) {
        mkdir($path);
    }

    $writer->openToFile($path . '/teachers.csv'); // write data to a file or to a PHP stream

    foreach ($teacherlist as $teachers) {
        foreach ($teachers as $teacher) {
            // TODO: Program code is currently -1 as a placeholder
            $writer->addRow([$teacher->email, $teacher->firstname, $teacher->lastname, $teacher->password, $teacher->confirmed, -1]); // add a row at a time
        }
    }

    $writer->close();
}

/**
 * This file builds and exports the teachers.
 * first_initial, last_name, username, password, password_confirm, teacher_email, program_code, class_period.
 */
function tci_export_students($studentlist) {

}