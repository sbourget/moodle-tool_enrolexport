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
 * Management of the exports.
 *
 * @package   tool_enrolexport
 * @copyright 2018 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once('edit_form.php');

$id = optional_param('id', 0, PARAM_INT); // 0 means create new entry.

require_login();
$context = context_system::instance();
if (!has_capability('tool/enrolexport:manageexports', $context)) {
    print_error('accessdenied', 'tool_enrolexport');
}

$PAGE->set_context($context);
$returnurl = new moodle_url("/$CFG->admin/tool/enrolexport/configure.php", array());
$PAGE->set_url(new moodle_url("/$CFG->admin/tool/enrolexport/edit.php", array()));
$PAGE->set_pagelayout('standard');
if ($id > 0) {
    // Updating an existing record.
    $strtitle = get_string('editexport', 'tool_enrolexport');

    $mform = new export_edit_form($PAGE->url, false, $id);
    $record = $DB->get_record('tool_enrolexport', array('id' => $id), '*', MUST_EXIST);
    $record->courses = explode(",", $record->courses);
    $mform->set_data($record);
} else {
    // Adding a new record.
    $strtitle = get_string('addexport', 'tool_enrolexport');

    $mform = new export_edit_form($PAGE->url, true, $id);
    $record = new stdClass;
    $record->id = 0;
    $mform->set_data($record);
}
if ($mform->is_cancelled()) {

    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    if ($data->id == 0) {
        $data->courses = implode(",", $data->courses);
        $id = $DB->insert_record('tool_enrolexport', $data);
        // Trigger event about adding the export.
        $params = array('context' => $context, 'objectid' => $id);
        $event = \tool_enrolexport\event\export_added::create($params);
        $event->add_record_snapshot('tool_enrolexport', $data);
        $event->trigger();
    } else if (isset($data->id) && (int)$data->id > 0) {
        $data->courses = implode(",", $data->courses);
        $id = $DB->update_record('tool_enrolexport', $data);
        // Trigger event about updating the export.
        $params = array('context' => $context, 'objectid' => $data->id);
        $event = \tool_enrolexport\event\export_updated::create($params);
        $event->add_record_snapshot('tool_enrolexport', $data);
        $event->trigger();
    }

    redirect($returnurl);

} else {

    $strmanageexports = get_string('manageexports', 'tool_enrolexport');
    $PAGE->navbar->add(get_string('pluginname', 'tool_enrolexport'), $returnurl);
    $PAGE->navbar->add($strmanageexports);
    $PAGE->set_title($strmanageexports);
    $PAGE->set_heading(format_string($strmanageexports));

    $PAGE->set_title($strtitle);
    $PAGE->set_heading($strtitle);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strtitle, 2);

    $mform->display();

    echo $OUTPUT->footer();
}
