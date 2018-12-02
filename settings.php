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
 * Plugin settings.
 *
 * @package    tool
 * @subpackage enrolexport
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
if ($hassiteconfig) {
    
    // First get a list of exporters with there own settings pages. If there none,
    // we use a simpler overall menu structure.
    $formats = core_component::get_plugin_list_with_file('enrolexporter', 'settings.php', false);
    $formatbyname = array();
    foreach ($formats as $format => $formatdir) {
        $strformatname = get_string('pluginname', 'enrolexporter_'.$format);
        $formatsbyname[$strformatname] = $format;
    }
    core_collator::ksort($formatsbyname);

    // Add actual export settings under plugin.
    $mysettings = new admin_settingpage('tool_enrolexport', get_string('pluginnamesettings', 'tool_enrolexport'));

    // Enable the exporter.
    $mysettings->add(new admin_setting_configcheckbox(
        'tool_enrolexport/enableexport',
        new lang_string('enableexport', 'tool_enrolexport'),
        '',
        1
    ));

    // Specify the export location.
    $mysettings->add(new admin_setting_configdirectory(
            'tool_enrolexport/exportpath',
            new lang_string('exportpath',  'tool_enrolexport'),
            new lang_string('exportpath_help', 'tool_enrolexport'), $CFG->dataroot));

    // Generate the settings tree.
    $ADMIN->add('tools', new admin_category('toolsettingsexportformat',
            get_string('pluginname', 'tool_enrolexport'), $this->is_enabled() === false));
    $ADMIN->add('toolsettingsexportformat', $mysettings);

    // Add settings pages for the exporter subplugins.
    foreach ($formatsbyname as $strformatname => $format) {
        $formatname = $format;
        $mysettings = new admin_settingpage('toolsettingsexportformat'.$formatname,
                $strformatname, 'moodle/site:config', $this->is_enabled() === false);
        if ($ADMIN->fulltree) {
            include($CFG->dirroot . "/$CFG->admin/tool/enrolexport/format/$formatname/settings.php");
        }
        if (!empty($mysettings)) {
            $ADMIN->add('toolsettingsexportformat', $mysettings);
        }
    }
    
}
