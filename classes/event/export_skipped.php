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
 * The tool_enrolexport exporter added event.
 *
 * @package    tool_enrolexport
 * @copyright  2019 Adam Yarris
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolexport\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The tool_enrolexport exporter added event.
 *
 * @package    tool_enrolexport
 * @copyright  2019 Adam Yarris
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_skipped extends \core\event\base {
    /**
     * Init method
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('exportskipped', 'tool_enrolexport');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "Skipping export for course id '$this->courseid' as no teachers are found";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        global $CFG;
        return new \moodle_url("/$CFG->admin/tool/enrolexport/configure.php");
    }



    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        // Make sure this class is never used without proper object details.
        if (!$this->contextlevel === CONTEXT_SYSTEM) {
            throw new \coding_exception('Context level must be CONTEXT_SYSTEM.');
        }
    }
}
