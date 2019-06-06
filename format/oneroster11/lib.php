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

require_once($CFG->libdir .'/spout/src/Spout/Autoloader/autoload.php');

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

/**
 * Exports data for oneroster 1.1.
 *
 * @param stdClass $settings The export data from the database.
 */
function enrolexporter_oneroster11_export($settings) {
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
        ["source.systemName", get_string('frontpage', 'fullname')],
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
        // TODO: At the end
    ]);
}

function oneroster_export_academicsessions($exportname) {
    create_csv('academicSessions', $exportname, [
        ['sourcedId', 'title', 'type', 'startDate', 'endDate', 'schoolyear'],
        [get_config('enrolexporter_oneroster11', 'academicsessions_sourcedId'), get_config('enrolexporter_oneroster11', 'academicsessions_title'), 'schoolYear', 'START DATE', 'END DATE', 'SCHOOL YEAR'],
    ]);
}

function oneroster_export_enrollments($exportname) {
    // TODO: This links other files together. I'll add the vales when the relative files are completed
    create_csv('enrollments', $exportname, [
        ['sourcedId', 'classSourcedId', 'schoolSourceId', 'userSourceId', 'role'],
        ['', '', '', '', '']
    ]);
}

// Each COURSE in moodle is a CLASS in OneRoster, and each GROUP in moodle is a CLASS in OneRoster
function oneroster_export_classes($exportname) {
    $rows = [['sourcedId', 'title', 'grade', 'courseSourceId', 'classType', 'schoolSourcedId', 'termSourcedId']];

    foreach (get_courses() as $course) {
        foreach (groups_get_all_groups($course->id) as $class) {
            if ($class->idnumber !== '') {
                // TODO: Make NA the grade in a custom field
                array_push($rows, [$class->id, $course->fullname . " " . $class->name, 'NA', '', get_config('enrolexporter_oneroster11', 'org_id')]);
            }
        }
    }

    create_csv('classes', $exportname, [
        ['sourcedId', '']
    ]);
}

function oneroster_export_courses($exportname) {
    create_csv('courses', $exportname, [
        // TODO: At the end
    ]);
}

function oneroster_export_users($exportname) {
    create_csv('users', $exportname, [
        // TODO: At the end
    ]);
}

function oneroster_export_orgs($exportname) {
    create_csv('orgs', $exportname, [
        // TODO: At the end
    ]);
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