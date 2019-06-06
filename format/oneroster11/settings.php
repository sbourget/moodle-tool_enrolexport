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
 * Version details.
 *
 * @package    enrolexporter_oneroster11
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


if ($ADMIN->fulltree) {
    // Introductory explanation.
    $mysettings->add(new admin_setting_heading('enrolexporter/oneroster11', '',
            new lang_string('fieldmappings', 'enrolexporter_oneroster11')));

    $url = new moodle_url("/$CFG->admin/tool/enrolexport/format/oneroster11/configure.php");
    $link = '<a href="' . $url . '">' . get_string('fieldmappings', 'enrolexporter_oneroster11') . '</a>';
    $mysettings->add(new admin_setting_heading('toolsettingsfieldmappings', '', $link));

    $mysettings->add(new admin_setting_configtext('enrolexporter_oneroster11/org_id',
        get_string('org_id', 'enrolexporter_oneroster11'),
        get_string('org_id_desc', 'enrolexporter_oneroster11'), '', PARAM_ALPHANUMEXT));

    // TODO: Add date picker for startDate and endDate
}
