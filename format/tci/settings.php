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
 * @package    tool_enrolexporter_tci
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Introductory explanation.
    $mysettings->add(new admin_setting_heading('enrolexporter/tci', '',
        new lang_string('fieldmappings', 'enrolexporter_tci')));

    $url = new moodle_url("/$CFG->admin/tool/enrolexport/format/tci/configure.php");
    $link = '<a href="' . $url . '">' . get_string('fieldmappings', 'enrolexporter_tci') . '</a>';
    $mysettings->add(new admin_setting_heading('toolsettingsfieldmappings', '', $link));

    $mysettings->add(new admin_setting_heading('studentheading',
        get_string('studentmappings', 'enrolexporter_tci'), ''));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/student_firstname',
        get_string('student_firstname', 'enrolexporter_tci'),
        get_string('student_firstname_desc', 'enrolexporter_tci'), 'firstname', array(
            'firstname' => 'firstname',
            'phonetic' => 'firstnamephonetic',
            'alternate' => 'alternatename',
            'initial' => 'firstname initial'
        )));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/student_lastname',
        get_string('student_lastname', 'enrolexporter_tci'),
        get_string('student_lastname_desc', 'enrolexporter_tci'), 'lastname', array(
            'lastname' => 'lastname',
            'phonetic' => 'lastnamephonetic',
            'initial' => 'lastname initial'
        )));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/student_username',
        get_string('student_username', 'enrolexporter_tci'),
        get_string('student_username_desc', 'enrolexporter_tci'), 'username', array(
            'username' => 'username',
            'email' => 'email'
        )));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/student_password',
        get_string('student_password', 'enrolexporter_tci'),
        get_string('student_password_desc', 'enrolexporter_tci'), 'random', array(
            'random' => 'Random',
            'id_username' => 'ID + username',
            'id_email' => 'ID + email',
            'id_firstname' => 'ID + firstname',
            'id_lastname' => 'ID + lastname'
        )));

    $mysettings->add(new admin_setting_heading('teacherheading',
        get_string('teachermappings', 'enrolexporter_tci'), ''));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/teacher_email',
        get_string('teacher_email', 'enrolexporter_tci'),
        get_string('teacher_email_desc', 'enrolexporter_tci'), 'email', array(
            'email' => 'email',
            'icq' => 'icq',
            'skype' => 'skype',
            'yahoo' => 'yahoo',
            'aim' => 'aim',
            'msn' => 'msn',
        )));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/teacher_firstname',
        get_string('teacher_firstname', 'enrolexporter_tci'),
        get_string('teacher_firstname_desc', 'enrolexporter_tci'), 'firstname', array(
            'firstname' => 'firstname',
            'phonetic' => 'firstnamephonetic',
            'alternate' => 'alternatename',
            'initial' => 'firstname initial'
        )));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/teacher_lastname',
        get_string('teacher_lastname', 'enrolexporter_tci'),
        get_string('teacher_lastname_desc', 'enrolexporter_tci'), 'lastname', array(
            'lastname' => 'lastname',
            'phonetic' => 'lastnamephonetic',
            'initial' => 'lastname initial'
        )));

    $mysettings->add(new admin_setting_configselect('enrolexporter_tci/teacher_password',
        get_string('teacher_password', 'enrolexporter_tci'),
        get_string('teacher_password_desc', 'enrolexporter_tci'), 'random', array(
            'random' => 'Random',
            'id_username' => 'ID + username',
            'id_email' => 'ID + email',
            'id_firstname' => 'ID + firstname',
            'id_lastname' => 'ID + lastname'
        )));
}
