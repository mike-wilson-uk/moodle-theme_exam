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

namespace theme_exam\output;

use coding_exception;
use html_writer;
use html_table;
use html_table_row;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use paging_bar;
use context_course;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_exam
 * @copyright  2019 Mike Wilson - University of Portsmouth
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \theme_boost\output\core_renderer {
    
    //Use the exam theme header.mustache
    public function full_header() {
        global $PAGE;

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        return $this->render_from_template('theme_exam/header', $header);
    }
    
        /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $SITE;

        $context = $form->export_for_template($this);

        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true,
            ['context' => context_course::instance(SITEID), "escape" => false]);

        return $this->render_from_template('core/loginform', $context);
    }
        
    //Output a div of server info including theme name and version, moodle version and server name
    public function server_info() {
        
        global $CFG, $PAGE;
        
        $seperator = ' | ';        
        
        $theme_label = get_string('theme_label', 'theme_exam');        
        $theme_name = ucfirst($PAGE->theme->name);
        $theme_version = $PAGE->theme->settings->version;
                
        $moodle_version_label = get_string('moodle_version_label', 'theme_exam');
        $full_version = explode(' ',$CFG->release);
        $moodle_version  = $full_version[0];
        
        $server_label = get_string('server_label', 'theme_exam');
        $server_name = php_uname('n');
        
        $server_info = $theme_label.' '.$theme_name.'. '.$theme_version.''.$seperator.''.$moodle_version_label.' '.$moodle_version.''.$seperator.''.$server_label.' '.$server_name;
    
        $output = html_writer::tag('div', "$server_info", array('class'=>'moodle_server_info'));     
        
        return $output;
        
    }
    
    //Output users first and last
    public function user_fullname() {
        
        global $USER;
        
        if (isloggedin()) {
        $output = $USER->firstname.' '.$USER->lastname;  
        }
        else $output = '';
        
        return $output;
        
    }
    
    // Display link back to exam list on quiz start page
    public function back_to_exam_list(){

        global $CFG, $PAGE;
        
        //Check for first page of quiz
        if( preg_match( '/^page-mod-quiz-view/', $PAGE->bodyid)){
                $link =  html_writer::link(new moodle_url('/', array('redirect'=>0)), get_string('backtolist','theme_exam'),array('class' => 'btn btn-primary'));
            }
            else $link = '';
            
            return $link;

    }
    
    // Display quit link if student is in a lti package (to enable student to leave after completing evasys survey)
    public function quit_link(){
        global $PAGE;
        
        //Check that we're on the lti tool
        if( preg_match( '/^page-mod-lti-view/', $PAGE->bodyid)){
                $quit_link_text = 'Click here AFTER completing the survey.';
                $quit = html_writer::link('http://exams.port.ac.uk/complete/', $quit_link_text);
                $link = html_writer::tag('div', "$quit", array('class'=>'quit-link btn-primary')); 
                $link .= html_writer::end_tag('div');
            }
            else $link = '';
            
            return $link;

    }
    
    // Display the list of exams for date selected (default is today)
    public function display_exam_list(){
        
        //Override date if selected from calendar
        $today = date('d/m/Y',time());

	if (!isset($_POST['date'])){
		$date = $today;
	}
	
	if (isset($_POST['date'])){
		$date = $_POST['date'];
	}
        
        global $CFG,$DB,$USER,$PAGE;
        $html = '';
        //check we're on the front page
        if( preg_match( '/^page-site-index/', $PAGE->bodyid)){
                   
            if ($USER->id == 0){
                $html = "<div class='alert alert-warning'>
                  You need to login to see the list of exams running today. <a href='$CFG->wwwroot/login/'>Click here to login</a>
                </div>";
            }
            elseif ($USER->id > 0){            
                $userid = $USER->id;
                $wwwroot = $CFG->wwwroot;
                $email = explode('@',$USER->email);
                $role = '';
                if ($email[1] == 'port.ac.uk'){
                    $role = 'staff';
                }
                else $role = 'student';
                
                $tag_id = $PAGE->theme->settings->tag_id;
                $available = 0;
                $open = 0;
            
                //Get course categories
                $categories = "SELECT id,name,idnumber,path FROM mdl_course_categories";
                
                $category = array();
                
                $get_categories = $DB->get_records_sql($categories);
        
                foreach ($get_categories as $cat){
                    $catid = $cat->id;
                    $catname = $cat->name;
                    $catidnumber = $cat->idnumber;
                    $catpath = $cat->path;
                    $path = explode('/',$catpath);

                    $faculty = $path[1];
                    if (isset($path[2])){
                        $dept = $path[2];
                    }
                    else $dept = '';
                    
                    $cats = array('catid' => $catid, 'catname' => $catname, 'catidnumber' => $catidnumber, 'faculty' => $faculty, 'dept' => $dept);
                        
                    array_push($category, $cats);
                                        
                }
        
                    function origin($id,$category){
                        $name = '';
                            foreach ($category as $row) {
                            if ($id == $row['catid']){
                                $name = $row['catname'];
                            }
                        }
                        return $name;
                    }
            if ($role == 'student'){

                    //Get current users course enrolments        
                    $enrolments = "SELECT mdl_course.id AS courseid,mdl_role_assignments.roleid,mdl_user_enrolments.status
                    FROM mdl_course
                    INNER JOIN mdl_context ON mdl_course.id = mdl_context.instanceid
                    INNER JOIN mdl_role_assignments ON mdl_context.id = mdl_role_assignments.contextid
                    INNER JOIN mdl_user ON mdl_role_assignments.userid = mdl_user.id
                    INNER JOIN mdl_enrol ON mdl_enrol.courseid = mdl_course.id
                    INNER JOIN mdl_user_enrolments ON mdl_user_enrolments.enrolid = mdl_enrol.id AND mdl_user_enrolments.userid = mdl_user.id
                    WHERE mdl_user.id = $userid
                    AND mdl_user_enrolments.status = 0
                    AND (mdl_user_enrolments.timeend = 0 OR mdl_user_enrolments.timeend > extract(epoch from now() at time zone 'utc'))";

                    $get_enrolments = $DB->get_records_sql($enrolments); 

                    $courses = array();
                    $courselist = '';

                    foreach ($get_enrolments as $enrol){

                        $courseid = $enrol->courseid;
                        $roleid = $enrol->roleid;
                        $status = $enrol->status;

                        $course = array('courseid' => $courseid,'roleid' => $roleid);
                        array_push($courses, $course);

                        $courselist .= ",$courseid";

                    }
                    $courselist = substr($courselist, 1); // remove leading ","
                    $courses = "AND mdl_course.id in($courselist)";
            }
            else $courses = '';            
        
            //Get exam tagged quizzes
            $tagged = "SELECT mdl_context.instanceid,mdl_course_modules.instance,mdl_course_modules.course,mdl_quiz.name,
            mdl_course.fullname,mdl_course.shortname,mdl_quiz.timeopen,mdl_course_modules.availability,mdl_course_categories.path as path
            FROM mdl_tag
            INNER JOIN mdl_tag_instance ON mdl_tag.id = mdl_tag_instance.tagid
            INNER JOIN mdl_context ON mdl_context.instanceid = mdl_tag_instance.itemid
            INNER JOIN mdl_course_modules ON mdl_context.instanceid = mdl_course_modules.id
            INNER JOIN mdl_quiz ON mdl_course_modules.instance = mdl_quiz.id
            INNER JOIN mdl_course ON mdl_course_modules.course = mdl_course.id
            INNER JOIN mdl_course_categories ON mdl_course.category = mdl_course_categories.id
            WHERE mdl_tag.id = $tag_id
            AND mdl_course_modules.deletioninprogress = 0
            $courses
            GROUP by instanceid,mdl_course_modules.instance,mdl_course_modules.course,mdl_quiz.name,
            mdl_course.fullname,mdl_course.shortname,mdl_quiz.timeopen,mdl_course_modules.availability,mdl_course_categories.path";

                $get_tagged = $DB->get_records_sql($tagged); 

                $table = new html_table();
                
                $table->head = array(get_string('exam_link', 'theme_exam'),get_string('date', 'theme_exam'),get_string('unit_name', 'theme_exam'),get_string('faculty', 'theme_exam'),get_string('dept', 'theme_exam'));
                
                foreach ($get_tagged as $tag){
                    $d = '';
                    $timeopen = '';
                    $availability = '';
                    
                    $quizname = $tag->name;
                    $fullname = $tag->fullname;
                    $shortname = $tag->shortname;
                    $instanceid = $tag->instanceid;
                    $availability = $tag->availability;
                    $timeopen= $tag->timeopen;
                    $check = $tag->path;
                    $path = explode('/',$tag->path);
                    
                    $fid = $path[1];                                    
                    $faculty = origin($fid,$category);
                    
                    if (isset($path[2])){
                        $did = $path[2];
                        $dept = origin($did,$category);
                    }
                    else $dept == '';
                    
                    //check if there an OPEN TIME set
                    if (isset($timeopen)){        
                        //set this as the exam date
                        $examdate = date('d/m/Y',$timeopen);
                            //if exam date is the same as the date selected in the date picker, we want to see the exam in the table
                            if ($examdate == $date){
                                $open = 1;
                            }
                    }
                    //check if there's RESTRICT ACCESS availability
                    if (isset($availability)){
                    //decode availability
                    $availability = json_decode($availability,true);    
                        //print_r($availability);
                    //loop through conditions
                    foreach ($availability['c'] as $avail){
                        //$avail->d
                        $type = $avail['type'];
                        //check if a date restriction is set
                        if (isset($avail['d'])){
                            $d = $avail['d'];
                        }
                        else $t = '';
                        //get the timestamp for the date restriction
                        if (isset($avail['t'])){
                            $t = $avail['t'];
                        }
                        else $t = 0;
                        //check there's an available from timestamp
                        if ($d == '>='){
                            //set this as the exam date
                            $examdate = date('d/m/Y',$t);
                                //if exam date is the same as the date selected in the date picker, we want to see the exam in the table
                                if ($examdate == $date){
                                    $available = 1;
                                }
                            }
                        }
                    }
                    if ($available + $open > 0){
                    
                        $quizURL = $wwwroot.'/mod/quiz/view.php';

                        $url = new moodle_url($quizURL, array('id'=>$instanceid));

                        $examurl = html_writer::link($url, $quizname, $attributes = null);

                        $row = new html_table_row(array($examurl, $date, "$fullname ($shortname)", $faculty, $dept));                        $row->attributes['data-id'] = '1';
                        $table->data[] = $row;
                    } 
                    $open = 0;
                    $available = 0;
                }
                $html = html_writer::table($table);           
            }
        }
        else $html = '';
        
        return "<div class='exam_table hideonerror'>".$html."<div>";
    }    
    
    public function exam_title(){
    //Display page title
        
        global $PAGE;
        
        $today = date('d/m/Y',time());
        if (!isset($_POST['date'])){
            $date = $today;
        }
        if(isset($_POST['date'])){
            $date = $_POST['date'];
        }
        
        function title_header($contents){
        //Return structure of exam_title
        $html = html_writer::start_tag('div', array('class' => 'exam-title-spacer'));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::start_tag('div', array('class' => 'card exam-title'));
            $html .= html_writer::start_tag('div', array('class' => 'card-body'));
                $html .= html_writer::start_tag('div', array('class' => 'd-flex'));
                    $html .= html_writer::start_tag('div', array('class' => 'mr-auto'));
                        $html .= html_writer::start_tag('div', array('class' => 'page-context-header'));
                            $html .= html_writer::start_tag('div', array('class' => 'page-header-headings'));
                                $html .= html_writer::tag('h1', $contents , array('class' => 'quiz_title hideonerror'));
                            $html .= html_writer::end_tag('div');
                        $html .= html_writer::end_tag('div');
                    $html .= html_writer::end_tag('div');
                $html .= html_writer::end_tag('div');
            $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');
        
        return $html;
        }
        
        if(preg_match( '/^page-site-index/', $PAGE->bodyid)){
            $text = get_string('exam_list_title','theme_exam');
            $contents = $text.''.$date;
            return title_header($contents);
        }
        elseif( preg_match( '/^page-mod-quiz-view/', $PAGE->bodyid) ||
                preg_match( '/^page-mod-quiz-attempt/', $PAGE->bodyid) ||
                preg_match( '/^page-mod-quiz-summary/', $PAGE->bodyid) ||
                preg_match( '/^page-mod-quiz-review/', $PAGE->bodyid)){
            $contents = $PAGE->title;
            return title_header($contents);
        }
        else {
                $page_title = $PAGE->title;
                return $page_title;
        }        
    }
    
    public function process_attempt(){
        //Display error message content if process attempt is triggered (already attempted quiz etc)
        $hideonerror = '';
        
        //$processattempt = 'processattempt.php';
        $processattempt = explode('/',$_SERVER['PHP_SELF']);
        
        if(end($processattempt) == 'processattempt.php'){
        $hideonerror = "<style>
                            #page-site-index div[role='main'] {
                                display: inline !important;
                            }
                        </style>";
                } 
        return $hideonerror;
    }
        
    public function detect_exam_browser_navbar(){
    //Establish device type for navbar colour (red for non-exam browser, blue for seb or chrome os)
	$device = $_SERVER['HTTP_USER_AGENT'];
        $style = "id='test'";
		
        if (strpos($device, 'CrOS') == FALSE && strpos($device, 'SEB') == FALSE)
        {
            $style = "id='non-exam-browser'";
        }
        else $style = "id='exam-browser'";
        
        return $style;
    }
    
    public function detect_exam_browser_alert(){
        //Establish device type for alert message on exam list page
        global $PAGE;
        $device = $_SERVER['HTTP_USER_AGENT'];
	
        if (isloggedin() && preg_match( '/^page-site-index/', $PAGE->bodyid)){
            $non_exam_browser_message = get_string('non_exam_browser_message', 'theme_exam');
            if (strpos($device, 'CrOS') == FALSE && strpos($device, 'SEB') == FALSE){
                    $alert = "<div class='alert alert-danger hideonerror' role='alert'>
                                 $non_exam_browser_message
                              </div>";
            } else $alert = '';
        } else $alert = '';
        
        return $alert;
    }
    
    // Date picker
    public function date_picker(){
        
        global $CFG,$PAGE,$USER;

        if (isloggedin() && preg_match( '/^page-site-index/', $PAGE->bodyid)){
            
            $userid = $USER->id;
            $wwwroot = $CFG->wwwroot;
            $email = explode('@',$USER->email);
            $role = '';
            if ($email[1] == 'port.ac.uk' || $email[1] == 'blackhole.port.ac.uk'){
                $role = 'staff';
            }
            else $role = 'student';
            
            if ($role == 'staff'){
            $date = '';
            
            //Set date as today or get it from form submission
            $today = date('d/m/Y',time());
            if (!isset($_POST['date'])){
		$date = $today;
            }
            if(isset($_POST['date'])){
                $date = $_POST['date'];
            }
            $link = $CFG->wwwroot."/?redirect=0";
            
        $datepicker = "
        <script src='theme/exam/datepicker/dist/js/bootstrap-datepicker.min.js' type='text/javascript'></script>           
        <link href='theme/exam/datepicker/dist/css/bootstrap-datepicker3.min.css' rel='stylesheet' type='text/css'/>
        
            <form action='$link' method='post' id='date-selector'>
                <div class='input-group date'>
                    <input type='text' class='form-control' name='date' id='date' value='$date'>
                    <span class='input-group-addon'>
                    <i class='glyphicon glyphicon-th'></i></span>
                </div>
            </form>
            <script>
                $('#date-selector .input-group.date').datepicker({
                    format: 'dd/mm/yyyy',
                    daysOfWeekHighlighted: '1,2,3,4,5',
                    autoclose: true
                });
                
                $('#date').change(function() {
                    this.form.submit();
                 });
            </script>";
            }
            else $datepicker = '';
        }
        else $datepicker = '';
        
        return $datepicker;
    }
}