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
 * @package    enrolexporter_oneroster11
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/spout/src/Spout/Autoloader/autoload.php');

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

/**
 * Exports data for oneroster 1.1.
 *
 * @param stdClass $settings The export data from the database.
 */
function enrolexporter_oneroster11_export($settings) {
    echo 'Oneroster';
    global $DB;
    $courses = explode(",", $settings->courses);
    $studentlist = array();
    $teacherlist = array();
    $coursemap = array();

    $record = $DB->get_records_select('enrolexporter_tci', false);

    foreach ($record as $id => $entry) {
        $exploded = explode(',', $record[$id]->course);
        foreach ($exploded as $mappedcode) {
            $coursemap[$mappedcode] = $record[$id]->programcode; // For the actual 'programcode' field
        }
    }

    foreach ($courses as $courseid) {
        $context = context_course::instance($courseid);
        $teacherlist[$courseid] = get_enrolled_users($context, 'enrolexporter/tci:includeinexportasteacher');
        $studentlist[$courseid] = get_enrolled_users($context, 'enrolexporter/tci:includeinexportasstudent');
    }

    oneroster_export_manifest($settings->name);
    oneroster_export_classresources($settings->name);
    oneroster_export_courseresources($settings->name);
    oneroster_export_resources($settings->name);
    oneroster_export_academicsessions($settings->name);
    oneroster_export_enrollments($settings->name);
    oneroster_export_classes($settings->name);
    oneroster_export_courses($settings->name);
    oneroster_export_users($settings->name);
    oneroster_export_orgs($settings->name);
}

/**
 * This file builds and exports the teachers.
 * The teacher file needs: Email, Firstname, Lastname, password, password confirm, programcode.
 */
function oneroster_export_manifest($exportname) {
    global $CFG;
    create_csv('manifest', $exportname, [
        ["manifest.version", "1.0"],
        ["oneroster.version", "1.1"],
        ["file.academicSessions", "bulk"],
        ["file.categories", "absent"],
        ["file.classes", "bulk"],
        ["file.classResources", "absent"],
        ["file.courses", "bulk"],
        ["file.courseResources", "absent"],
        ["file.demographics", "absent"],
        ["file.enrollments", "bulk"],
        ["file.lineItems", "absent"],
        ["file.orgs", "bulk"],
        ["file.resources", "absent"],
        ["file.results", "absent"],
        ["file.users", "bulk"],
        ["source.systemName", $CFG->fullname],
        ["source.systemCode", $CFG->wwwroot],
    ]);
}

function oneroster_export_classresources($exportname) {
    // TODO: Optional
    create_csv('classResources', $exportname, [[]]);
}

function oneroster_export_courseresources($exportname) {
    // TODO: Optional
    create_csv('courseResources', $exportname, [[]]);
}

function oneroster_export_resources($exportname) {
    create_csv('resources', $exportname, [
        ['sourcedId', 'vendorResourceId', 'title', 'vendorId'],
        [get_config('enrolexporter_oneroster11', 'resourcesmastercode'), get_config('enrolexporter_oneroster11', 'resourcesmastercode'), get_config('enrolexporter_oneroster11', 'title'), 'vnd.mhe']
    ]);
}

function oneroster_export_academicsessions($exportname) {
    create_csv('academicSessions', $exportname, [
        ['sourcedId', 'title', 'type', 'startDate', 'endDate', 'schoolyear'],
        [get_config('enrolexporter_oneroster11', 'academicsessions_sourcedId'), get_config('enrolexporter_oneroster11', 'academicsessions_title'), 'schoolYear', 'START DATE', 'END DATE', 'SCHOOL YEAR'],
    ]);
}

function oneroster_export_enrollments($exportname) {
    $rows = [['sourcedId', 'classSourcedId', 'schoolSourcedId', 'userSourcedId', 'role']];

    $orgId = get_config('enrolexporter_oneroster11', 'orgid');
    $capabilityUsers = get_user_from_roles();

    foreach (get_courses() as $course) {
        foreach (groups_get_all_groups($course->id) as $class) {

            foreach (groups_get_members($class->id) as $user) {
                if ($class->idnumber !== '') {
                    array_push($rows, ["$user->id$class->id", $class->id, $orgId, $user->id, get_capability($user, $capabilityUsers)]);
                }
            }

        }
    }

    create_csv('enrollments', $exportname, $rows);
}

function get_capability($user, $capabilityUsers) {
    foreach ($capabilityUsers as $specString => $users) {
        if (in_array($user, $users)) return $specString;
    }
    return 'student';
}

// Each COURSE in moodle is a CLASS in OneRoster, and each GROUP in moodle is a CLASS in OneRoster
function oneroster_export_classes($exportname) {
    $rows = [['sourcedId', 'title', 'grade', 'courseSourceId', 'classType', 'schoolSourcedId', 'termSourcedId']];

    foreach (get_courses() as $course) {
        foreach (groups_get_all_groups($course->id) as $class) {
            if ($class->idnumber !== '') {
                // TODO: Make NA the grade in a custom field
                array_push($rows, [$class->id, $course->fullname . " " . $class->name, 'NA', '', get_config('enrolexporter_oneroster11', 'orgid')]);
            }
        }
    }

    create_csv('classes', $exportname, $rows);
}

function oneroster_export_courses($exportname) {
    // From specs:
    //   subjectCodes - Not used for MHE integration - ca be left blank, any data will be ignored
    //   grade - If left blank, the student grade will be set to NA
    $rows = [['sourcedId', 'title', 'grade', 'orgSourcedId', 'subjectCodes']];

    foreach (get_courses() as $course) {
        // TODO: Add options for what name should be added
        array_push($rows, [$course->id, $course->fullname, 'NA', get_config('enrolexporter_oneroster11', 'orgid')]);
    }

    create_csv('courses', $exportname, $rows);
}

function oneroster_export_users($exportname) {
    // From specs:
    //   enabledUser - This field is required per OR v1.1 specs but is not utilized by MHE
    $rows = [['sourcedId', 'enabledUser', 'orgSourcedIds', 'role', 'username', 'givenName', 'familyName', 'email']];

    $orgId = get_config('enrolexporter_oneroster11', 'orgid');

    foreach (get_user_from_roles() as $specString => $users) {
        foreach ($users as $user) {
            array_push($rows, [$user->id, true, $orgId, $specString, $user->username, $user->firstname, $user->lastname, $user->email]);
        }
    }

    create_csv('users', $exportname, $rows);
}

function oneroster_export_orgs($exportname) {
    create_csv('orgs', $exportname, [
        ['sourcedId', 'name', 'type'],
        [get_config('enrolexporter_oneroster11', 'orgid'), get_config('enrolexporter_oneroster11', 'orgname'), get_config('enrolexporter_oneroster11', 'orgtype')]
    ]);
}

// Gets the string from the OR 1.1 spec and the users associated with it as an array
function get_user_from_roles() {
    $context = context_system::instance();
    $users = array();

    $permissionSpecMap = [
        'includeinexportasteacher' => 'teacher',
        'includeinexportasstudent' => 'student',
        'includeinexportasparent' => 'parent',
        'includeinexportasguardian' => 'guardian',
        'includeinexportasrelative' => 'relative',
        'includeinexportasaide' => 'aide',
        'includeinexportasadministrator' => 'administrator',
    ];
    foreach ($permissionSpecMap as $permission => $specString) {
        $roleRow = array();
        foreach (get_enrolled_users($context, 'enrolexporter/oneroster11:' . $permission) as $user) {
            array_push($roleRow, $user);
        }
        $users[$specString] = $roleRow;
    }

    return $users;
}

/**
 * Creates a CSV with the given parameters in the /oneroster/$exportname directory.
 *
 * @param string $filename The name of the file. This should not be dynamic.
 * @param string $exportname The export name derived from settings.
 * @param array $rows The 2D array of rows to add to the CSV.
 */
function create_csv($filename, $exportname, $rows) {
    $writer = WriterFactory::create(Type::CSV);
    $path = get_config('tool_enrolexport', 'exportpath') . '/oneroster11/' . clean_param($exportname, PARAM_PATH);

    if (!file_exists($path)) {
        mkdir($path);
    }

    $writer->openToFile("$path/$$filename.csv");
    $writer->addRows($rows);
    $writer->close();
}