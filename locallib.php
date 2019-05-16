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
 * Helper functions for plug-in.
 *
 * @package    tool_enrolexport
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Converts a string of course ids to shortnames.
 * @param string $ids
 * @return string
 */
function tool_enrolexport_courselist($ids) {
    global $DB;
    $coursename = array();
    $courses = explode(",", $ids);
    $results = $DB->get_records_list('course', 'id', $courses);
    foreach ($results as $result) {
        $coursename[] = $result->shortname;
    }
    return implode(", ", $coursename);
}
