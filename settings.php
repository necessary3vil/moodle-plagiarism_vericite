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
pl * plagiarism.php - allows the admin to configure plagiarism stuff
 *
 * @package   plagiarism_turnitin
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    require_once(dirname(dirname(__FILE__)) . '/../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/plagiarismlib.php');
    require_once($CFG->dirroot.'/plagiarism/vericite/lib.php');
    require_once($CFG->dirroot.'/plagiarism/vericite/plagiarism_form.php');

    require_login();
    admin_externalpage_setup('plagiarismvericite');
    $context = context_system::instance();

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");
    require_once('plagiarism_form.php');
    $mform = new plagiarism_setup_form();
    $plagiarismplugin = new plagiarism_plugin_vericite();

    if ($mform->is_cancelled()) {
        redirect('');
    }

    echo $OUTPUT->header();
    if (($data = $mform->get_data()) && confirm_sesskey()) {
        //set checkboxes to 0 is they aren't set
	if (!isset($data->vericite_use)) {
            $data->vericite_use = 0;
        }
	if (!isset($data->vericite_disable_dynamic_inline)) {
            $data->vericite_disable_dynamic_inline = 0;
        }
	if (!isset($data->vericite_enable_debugging)) {
            $data->vericite_enable_debugging = 0;
        }
        if (!isset($data->vericite_use_default)) {
            $data->vericite_use_default = 0;
        }
        if (!isset($data->vericite_student_score_default)) {
            $data->vericite_student_score_default = 0;
        }
        if (!isset($data->vericite_student_report_default)) {
            $data->vericite_student_report_default = 0;
        }
        if (!isset($data->vericite_student_score_default_forums)) {
            $data->vericite_student_score_default_forums = 0;
        }
        if (!isset($data->vericite_student_report_default_forums)) {
            $data->vericite_student_report_default_forums = 0;
        }
        //save each setting
	foreach ($data as $field=>$value) {
            if (strpos($field, 'vericite')===0) {
                set_config($field, $value, 'plagiarism');
            }
        }
        notify(get_string('savedconfigsuccess', 'plagiarism_vericite'), 'notifysuccess');
    }
    $plagiarismsettings = (array)get_config('plagiarism');
    $mform->set_data($plagiarismsettings);
    
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
    $mform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
