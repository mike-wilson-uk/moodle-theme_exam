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

defined('MOODLE_INTERNAL') || die();

/**
 * A login page layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$bodyattributes = $OUTPUT->body_attributes();

    //Renderers not currently supported for login page so function needs to live here for the timebeing
    function detect_exam_browser_login_header(){
    //Establish device type for login header colour (red for non-exam browser, blue for seb or chrome os)
	$device = $_SERVER['HTTP_USER_AGENT'];
		
        if (strpos($device, 'CrOS') == FALSE && strpos($device, 'SEB') == FALSE)
        {
            $background = "#C4014B";
        }
        else $background = "#00A0FF";
        
        $style = "<style>
                    #browser_detect{background-color: $background;}
                  </style>";
        
        return $style;
    }
        
echo detect_exam_browser_login_header();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes
];

echo $OUTPUT->render_from_template('theme_exam/login', $templatecontext);

