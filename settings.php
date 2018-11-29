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
 * @package    tool
 * @subpackage enrolexport
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
if ($hassiteconfig) {
    // Add link to use tool under "Courses".
    $ADMIN->add('courses', new admin_externalpage('toolenrolexport',
        get_string('exportenrolments', 'tool_enrolexport'), "$CFG->wwwroot/$CFG->admin/tool/enrolexport/index.php"));
    
    // Add actual export settings under plugin
    $settings = new admin_settingpage('tool_enrolexport', get_string('pluginname', 'tool_enrolexport'));
    $ADMIN->add('tools', $settings);
    
    // Enable the exporter.
    $settings->add(new admin_setting_configcheckbox(
        'tool_enrolexport/enableexport',
        new lang_string('enableexport', 'tool_enrolexport'),
        '',
        1
    ));
    
    // Specify the export location.
    $settings->add(new admin_setting_configdirectory(
            'tool_enrolexport/exportpath', 
            new lang_string('exportpath',  'tool_enrolexport'),
            new lang_string('exportpath_help', 'tool_enrolexport'), $CFG->dataroot));
    

}
