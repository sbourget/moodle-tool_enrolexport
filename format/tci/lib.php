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
 * @package    tool_enrolexporter_tci
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/spout/src/Spout/Autoloader/autoload.php');

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

$passlength = 12;

function enrolexporter_tci_export($settings) {
    global $DB;
    $courses = explode(",", $settings->courses);
    $studentlist = array();
    $teacherlist = array();
    $coursemap = array();

    $record = $DB->get_records_select('enrolexporter_tci', false);

    foreach ($record as $id => $entry) {
        $exploded = explode(',', $record[$id]->course);
        foreach ($exploded as $mappedcode) {
            $coursemap[$mappedcode] = $record[$id]->programcode; // For the actual 'programcode' field.
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

    $writer->openToFile($path . '/teachers.csv');

    foreach ($teacherlist as $courseid => $teachers) {
        $programcode = isset($coursemap[$courseid]) ? $coursemap[$courseid] : '';
        foreach ($teachers as $teacher) {
            $email = array(
                'email' => $teacher->email,
                'icq' => $teacher->icq,
                'skype' => $teacher->skype,
                'yahoo' => $teacher->yahoo,
                'aim' => $teacher->aim,
                'msn' => $teacher->msn
            )[get_config('enrolexporter_tci', 'teacher_email')];

            $firstname = array(
                'firstname' => $teacher->firstname,
                'phonetic' => $teacher->firstnamephonetic,
                'alternate' => $teacher->alternatename,
                'initial' => $teacher->firstname[0]
            )[get_config('enrolexporter_tci', 'teacher_firstname')];

            $lastname = array(
                'lastname' => $teacher->lastname,
                'phonetic' => $teacher->lastnamephonetic,
                'initial' => $teacher->lastname[0],
            )[get_config('enrolexporter_tci', 'teacher_lastname')];

            $password = array(
                'random' => generate_password(),
                'id_username' => $teacher->idnumber + $teacher->username,
                'id_email' => $teacher->idnumber + $teacher->email,
                'id_firstname' => $teacher->idnumber + $teacher->firstname,
                'id_lastname' => $teacher->idnumber + $teacher->lastname
            )[get_config('enrolexporter_tci', 'teacher_password')];

            $writer->addRow([$email, $firstname, $lastname, $password, $teacher->confirmed, $programcode]);
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

    $writer->openToFile($path . '/students.csv');

    $classperiod = 0;

    foreach ($studentlist as $courseid => $students) {
        $teachervalues = array_values($teacherlist[$courseid]);
        if (count($teacherlist[$courseid]) == 0) {
            // TODO: Add logging course skipped.
            continue;
        }

        $firstteacher = $teachervalues[0];

        $programcode = isset($coursemap[$courseid]) ? $coursemap[$courseid] : '';
        foreach ($students as $student) {
            $firstname = array(
                'firstname' => $student->firstname,
                'phonetic' => $student->firstnamephonetic,
                'alternate' => $student->alternatename,
                'initial' => $student->firstname[0],
            )[get_config('enrolexporter_tci', 'student_firstname')];

            $lastname = array(
                'lastname' => $student->lastname,
                'phonetic' => $student->lastnamephonetic,
                'initial' => $student->lastname[0]
            )[get_config('enrolexporter_tci', 'student_lastname')];

            $username = array(
                'username' => $student->username,
                'email' => $student->email,
            )[get_config('enrolexporter_tci', 'student_username')];

            $password = array(
                'random' => generate_password(),
                'id_username' => $student->idnumber + $student->username,
                'id_email' => $student->idnumber + $student->email,
                'id_firstname' => $student->idnumber + $student->firstname,
                'id_lastname' => $student->idnumber + $student->lastname
            )[get_config('enrolexporter_tci', 'student_password')];

            $writer->addRow([$firstname, $lastname, $username, $password,
                $password, $firstteacher->email, $programcode, $classperiod]);
        }
    }

    $writer->close();
}

function generate_password() {
    global $passlength;
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        ceil($passlength / strlen($x)))), 1, $passlength);
}