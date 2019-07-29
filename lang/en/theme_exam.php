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
 * Language file.
 *
 * @package   theme_exam
 * @copyright 2019 Mike Wilson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

//Accessibility block region and title
$string['region-accessibility'] = 'Accessibility';
$string['accessibility-options'] = 'Accessibility Options';
// The name of the second tab in the theme settings.
$string['advancedsettings'] = 'Advanced settings';
// The brand colour setting.
$string['brandcolor'] = 'Brand colour';
// The brand colour setting description.
$string['brandcolor_desc'] = 'The accent colour.';
// A description shown in the admin theme selector.
$string['choosereadme'] = 'Theme exam is a child theme of Boost.';
// Name of the settings pages.
$string['configtitle'] = 'Exam';
// Name of the first settings tab.
$string['generalsettings'] = 'General settings';
// The name of our plugin.
$string['pluginname'] = 'Exam';
// Preset files setting.
$string['presetfiles'] = 'Additional theme preset files';
// Preset files help text.
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
// Preset setting.
$string['preset'] = 'Theme preset';
// Preset help text.
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
// Enable exam workflow
$string['enable_exam_workflow'] = 'Enable exam workflow?';
// Enable exam workflow
$string['enable_exam_workflow_desc'] = 'Enabling the exam workflow keeps users within the quiz workflow starting on the front page list of exams. When the exam is completed users are sent to the complete exam page. Users that drop onto a page outside of the quiz workflow will be dropped back to the exam list on the frontpage.';
// Raw SCSS setting.
$string['rawscss'] = 'Raw SCSS';
// Raw SCSS setting help text.
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
// Raw initial SCSS setting.
$string['rawscsspre'] = 'Raw initial SCSS';
// Raw initial SCSS setting help text.
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Right';
//Footer server info
$string['theme_label'] = 'Theme: ';
$string['moodle_version_label'] = 'Moodle Version: ';
$string['server_label'] = 'Server: ';
//Back to exam list link
$string['backtolist'] = 'Back to exam list';
//exam-official tag
$string['tag_id'] = 'ID of exam-official tag from mdl_tag';
$string['tag_iddesc'] = 'Enter the id of the exam-official tag from mdl_tag. This used to establish exams to be displayed on the front page when user is logged in.';
//Exam table header
$string['exam_link'] = 'Exam Link';
$string['date'] = 'Date';
$string['unit_name'] = 'Unit Name';
$string['faculty'] = 'Faculty';
$string['dept'] = 'Dept';
//Non-exam browser warning alert message
$string['non_exam_browser_message'] = 'The browser you are using is not recommended for taking exams securely. The exam should work in most modern browsers, however we recommend using Safe Exam Browser or a Chromebook in exam mode to ensure the exam is conducted securely.';
//Exam list tile
$string['exam_list_title'] = 'Exam list for ';