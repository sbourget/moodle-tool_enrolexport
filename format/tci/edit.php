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
 * @package   enrolexporter_tci
 * @copyright 2019 Adam Yarris
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../../../config.php');
require_once('edit_form.php');

$id = optional_param('id', 0, PARAM_INT); // 0 means create new entry.

require_login();
$context = context_system::instance();
// TODO: Change permissions?
if (!has_capability('tool/enrolexport:manageexports', $context)) {
    print_error('accessdenied', 'tool_enrolexport');
}

$PAGE->set_context($context);
$returnurl = new moodle_url("/$CFG->admin/tool/enrolexport/format/tci/configure.php", array());
$PAGE->set_url(new moodle_url("/$CFG->admin/tool/enrolexport/format/tci/edit.php", array()));
$PAGE->set_pagelayout('standard');
if ($id > 0) {
    // Updating an existing record.
    $strtitle = get_string('editmapping', 'enrolexporter_tci');

    $mform = new mapping_edit_form($PAGE->url, false, $id);
    $record = $DB->get_record('enrolexporter_tci', array('id' => $id), '*', MUST_EXIST);
    $record->course = explode(",", $record->course);
    $mform->set_data($record);
} else {
    // Adding a new record.
    $strtitle = get_string('addmapping', 'enrolexporter_tci');

    $mform = new mapping_edit_form($PAGE->url, true, $id);
    $record = new stdClass;
    $record->id = 0;
    $mform->set_data($record);
}
if ($mform->is_cancelled()) {

    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    if ($data->id == 0) {
        $data->course = implode(",", $data->course);
        $id = $DB->insert_record('enrolexporter_tci', $data);

        // TODO: Events.
    } else if (isset($data->id) && (int)$data->id > 0) {
        $data->course = implode(",", $data->course);
        $id = $DB->update_record('enrolexporter_tci', $data);

        // TODO: Events.
    }

    redirect($returnurl);

} else {

    $strmanageexports = get_string('managemappings', 'enrolexporter_tci');
    $PAGE->navbar->add(get_string('fieldmappings', 'enrolexporter_tci'), $returnurl);
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
