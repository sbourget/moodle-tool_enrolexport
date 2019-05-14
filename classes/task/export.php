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
 * Export course enrollments.
 *
 * @package    tool_enrolexport
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolexport\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Export course enrollments.
 *
 * @package    tool_enrolexport
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export extends \core\task\scheduled_task {

    /**
     * get_name
     *
     * @return string
     */
    public function get_name() {
        return get_string('exportenrolmentstask', 'tool_enrolexport');
    }

    /**
     * Executes the prediction task.
     *
     * @return void
     */
    public function execute() {
        global $DB, $CFG;

        // Get list of defined exports.
        $rs = $DB->get_records('tool_enrolexport', null, 'name');

        // Loop through them executing each ine in turn.
        foreach ($rs as $index => $exporter) {
            // Get the exporter name.
            require_once("$CFG->dirroot/$CFG->admin/tool/enrolexport/format/$exporter->exporter/lib.php");
            $functionname = "enrolexporter_"."$exporter->exporter"."_export";
            $functionname($exporter);
        }

    }
}
