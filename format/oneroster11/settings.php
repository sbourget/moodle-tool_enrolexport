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

    $mysettings->add(new admin_setting_heading('orgheading',
        get_string('orgsettings', 'enrolexporter_oneroster11'), ''));

    $mysettings->add(new admin_setting_configtext('enrolexporter_oneroster11/orgid',
        get_string('orgid', 'enrolexporter_oneroster11'),
        get_string('orgid_desc', 'enrolexporter_oneroster11'), '', PARAM_ALPHANUMEXT));

    $mysettings->add(new admin_setting_configtext('enrolexporter_oneroster11/orgname',
        get_string('orgname', 'enrolexporter_oneroster11'),
        get_string('orgname_desc', 'enrolexporter_oneroster11'), '', PARAM_ALPHANUMEXT));

    $mysettings->add(new admin_setting_configselect('enrolexporter_oneroster/orgtype',
        get_string('orgtype', 'enrolexporter_oneroster11'),
        get_string('orgtype_desc', 'enrolexporter_oneroster11'), 'school', array(
            'school' => 'school',
            'local' => 'local',
            'state' => 'state',
            'national' => 'national'
        )));

    $mysettings->add(new admin_setting_heading('resourcesheading',
        get_string('resourcessettings', 'enrolexporter_oneroster11'), ''));

    $mysettings->add(new admin_setting_configtext('enrolexporter_oneroster11/resourcesmastercode',
        get_string('resourcesmastercode', 'enrolexporter_oneroster11'),
        get_string('resourcesmastercode_desc', 'enrolexporter_oneroster11'), '', PARAM_ALPHANUMEXT));

    $mysettings->add(new admin_setting_configtext('enrolexporter_oneroster11/resourcestitle',
        get_string('resourcestitle', 'enrolexporter_oneroster11'),
        get_string('resourcestitle_desc', 'enrolexporter_oneroster11'), '', PARAM_ALPHANUMEXT));

    // TODO: Add date picker for startDate and endDate
}
