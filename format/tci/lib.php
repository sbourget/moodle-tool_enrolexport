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
    global $DB;
    $courses = explode(",", $settings->courses);
    $studentlist = array();
    $teacherlist = array();
    $coursemap = array();

    $record = $DB->get_records_select('enrolexporter_tci', false);

    foreach ($record as $programcode => $entry) {
        $exploded = explode(',', $record[$programcode]->course);
        foreach ($exploded as $mappedcode) {
            $coursemap[$mappedcode] = $programcode;
        }
    }

    foreach ($courses as $courseid) {
        $context = context_course::instance($courseid);
        $teacherlist[$courseid] = get_enrolled_users($context, 'enrolexporter/tci:includeinexportasteacher');
        $studentlist[$courseid] = get_enrolled_users($context, 'enrolexporter/tci:includeinexportasstudent');
    }

    tci_export_teachers($settings->name, $teacherlist, $coursemap);
    tci_export_students($settings->name, $studentlist, $teacherlist, $coursemap);
}

/**
 * This file builds and exports the teachers.
 * The teacher file needs: Email, Firstname, Lastname, password, password confirm, programcode.
 */
function tci_export_teachers($exportname, $teacherlist, $coursemap) {
    // 1. Get enrolled users.
    // 2. Extract teachers.
    // 3. Create CSV.
    $writer = WriterFactory::create(Type::CSV);
    $path = get_config('tool_enrolexport', 'exportpath') . '/tci/' . clean_param($exportname, PARAM_PATH);

    if (!file_exists($path)) {
        mkdir($path);
    }

    $writer->openToFile($path . '/teachers.csv'); // write data to a file or to a PHP stream

    foreach ($teacherlist as $courseid => $teachers) {
        $programcode = isset($coursemap[$courseid]) ? $coursemap[$courseid] : '';
        foreach ($teachers as $teacher) {
            $writer->addRow([$teacher->email, $teacher->firstname, $teacher->lastname, $teacher->password, $teacher->confirmed, $programcode]);
        }
    }

    $writer->close();
}

/**
 * This file builds and exports the teachers.
 * first_initial, last_name, username, password, password_confirm, teacher_email, program_code, class_period.
 */
function tci_export_students($exportname, $studentlist, $teacherlist, $coursemap) {
    // 1. Get enrolled users.
    // 2. Extract students.
    // 3. Create CSV.
    $writer = WriterFactory::create(Type::CSV);
    $path = get_config('tool_enrolexport', 'exportpath') . '/tci/' . clean_param($exportname, PARAM_PATH);

    if (!file_exists($path)) {
        mkdir($path);
    }

    $writer->openToFile($path . '/students.csv'); // write data to a file or to a PHP stream

    $classperiod = 0;

    foreach ($studentlist as $courseid => $students) {
        $teachervalues = array_values($teacherlist[$courseid]);
        if (sizeof($teacherlist[$courseid]) == 0) {
            // TODO: Add logging course skipped
            continue;
        }

        $firstteacher = $teachervalues[0];

        $programcode = isset($coursemap[$courseid]) ? $coursemap[$courseid] : '';
         foreach ($students as $student) {
             $writer->addRow([$student->firstname[0], $student->lastname, $student->username, $student->password, $student->confirmed, $firstteacher->email, $programcode, $classperiod]);
         }
    }

    $writer->close();
}