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
 * @package    tool_enrolexporter
 * @subpackage enrolexport
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once('locallib.php');
global $DB;

$delete = optional_param('delete', 0, PARAM_INT);

require_login();
$context = context_system::instance();
if (!has_capability('tool/enrolexport:manageexports', $context)) {
    print_error('accessdenied', 'tool_enrolexport');
}

$strmanageexporters = get_string('manageexports', 'tool_enrolexport');

// Print the header.
$urlparams = array();
$baseurl = new moodle_url("/$CFG->admin/tool/enrolexport/configure.php", $urlparams);
$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_title($strmanageexporters);

if (($delete > 0) && confirm_sesskey()) {
    // Trigger event about deleting the export.
    $params = array('context' => $context, 'objectid' => $delete);
    $event = \tool_enrolexport\event\export_deleted::create($params);
    $event->trigger();

    $DB->delete_records('tool_enrolexport', array('id' => $delete));
}

$rs = $DB->get_records('tool_enrolexport', null, 'name');
if (!is_array($rs)) {
    $rs = array();
}

// Check to see if we have any records.
if (count($rs) == 0) {
    $add = 1;
}

echo $OUTPUT->header();
echo html_writer::start_tag('div', array('class' => 'content'));
echo html_writer::tag('h2', $strmanageexporters, array('class' => 'main'));

// Generate the table.
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $baseurl));

$table = new flexible_table('exporter-administration');

$table->define_columns(array('name', 'exporter', 'courses', 'icons'));
$table->define_headers(array(get_string('name', 'tool_enrolexport'),
                             get_string('exporter', 'tool_enrolexport'),
                             get_string('courses', 'tool_enrolexport'),
                             get_string('actions', 'moodle')));
$table->define_baseurl($baseurl);

$table->set_attribute('cellspacing', '0');
$table->set_attribute('id', 'exporters');
$table->set_attribute('class', 'generaltable generalbox');
$table->column_class('name', 'name');
$table->column_class('exporter', 'exporter');
$table->column_class('courses', 'courses');
$table->column_class('actions', 'actions');

$table->setup();

foreach ($rs as $index => $exporter) {

    // Generate Icons.
    $editurl = new moodle_url("/$CFG->admin/tool/enrolexport/edit.php", array('id' => $exporter->id));
    $editaction = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));

    $deleteurl = new moodle_url("/$CFG->admin/tool/enrolexport/configure.php",
                 array('delete' => $exporter->id, 'sesskey' => sesskey()));

    $deleteicon = new pix_icon('t/delete', get_string('delete'));
    $deleteaction = $OUTPUT->action_icon($deleteurl, $deleteicon,
                    new confirm_action(get_string('deleteexporterconfirm', 'tool_enrolexport', $exporter->name)));

    $icons = $editaction . ' ' . $deleteaction;

    $table->add_data(array($exporter->name,
                               get_string('pluginname', 'enrolexporter_'.$exporter->exporter),
                               tool_enrolexport_courselist($exporter->courses),
                               $icons));
}

$table->print_html();

echo html_writer::end_tag('form');
echo html_writer::start_tag('div', array('class' => 'actionbuttons'));

echo html_writer::empty_tag('hr', array());
$addurl = new moodle_url("/$CFG->admin/tool/enrolexport/edit.php", array());
echo $OUTPUT->single_button($addurl, get_string('addexporter', 'tool_enrolexport'), 'get');
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');


echo $OUTPUT->footer();
