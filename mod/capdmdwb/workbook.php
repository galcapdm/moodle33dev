<?php

require_once('../../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once('dwbworkbookbase.php');

// Generate a customised Digital Workbook
// CAPDM: 11-Jun-2013

    $id    = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $sid   = optional_param('dwb', 0, PARAM_INT);
    $nm    = optional_param('nm', '', PARAM_TEXT);
    $tt    = optional_param('taskticks', 0, PARAM_INT);

    if ($id) {
        $cm        = get_coursemodule_from_id('capdmdwb', $id, 0, false, MUST_EXIST);

	$course    = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$capdmdwb  = $DB->get_record('capdmdwb', array('id' => $cm->instance), '*', MUST_EXIST);
    }
    else die;

    require_login($course->id);

    // Create a Workbook to 'print'
//    $dwbo = "dwb_workbook_".$capdmdwb->role_id;  // Role specifies the type

    // Load the class
    //include_once("type/".$capdmdwb->role_id."/dwbworkbook.php");


    include_once("type/".$capdmdwb->role_id."/dwbworkbook.php");

//    // Instantiaite it
//    if (class_exists($dwbo)) {
//       $mydwb = new $dwbo($capdmdwb, $course, $cm, $sid);  // Create a specific instance
//    }

    // Now output it
//    echo($mydwb->render());
//    echo $OUTPUT->footer();