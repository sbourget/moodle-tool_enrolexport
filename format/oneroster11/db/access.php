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
 * Access control.
 *
 * @package    enrolexporter_oneroster11
 * @copyright  2018 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    // Used to identify teachers in the export.
    'enrolexporter/oneroster11:includeinexportasteacher' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW
    )],

    // Used to identify students in the export.
    'enrolexporter/oneroster11:includeinexportasstudent' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'student' => CAP_ALLOW
    )],

    // Used to identify students in the export.
    'enrolexporter/oneroster11:includeinexportasparent' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE
    ],

    // Used to identify students in the export.
    'enrolexporter/oneroster11:includeinexportasguardian' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE
    ],

    // Used to identify students in the export.
    'enrolexporter/oneroster11:includeinexportasrelative' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE
    ],

    // Used to identify students in the export.
    'enrolexporter/oneroster11:includeinexportasaide' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE
    ],

    // Used to identify students in the export.
    'enrolexporter/oneroster11:includeinexportasadministrator' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE
    ]

);
