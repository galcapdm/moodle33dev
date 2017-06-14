<?php

global $DB, $CFG;

require_once('../../config.php');
require_once(__DIR__ .'/../../lib/moodlelib.php');

require_login();

$op = required_param('op', PARAM_TEXT);
$value = optional_param('value', 0, PARAM_TEXT);

ob_start();
unset($ret);

$ret = array();

switch($op){
    // Get responses to a helpdesk request by id.
    case 0:
        $records = $DB->get_records('capdmhelpdesk_replies', array('replyto'=>1));
        // Build an array of arrays representing the records returned from the query.
        foreach($records as $r){
            $thisUser = array('id'=>$r->id, 'message'=>$r->message, 'submitdate'=> $r->submitdate);
            array_push($ret, $thisUser);
        }
        break;
    // Add a new message to the replies table.
    case 1:
        $newRec = new stdClass();

        $newRec->replyto = 1;
        $newRec->message = 'message text input';
        $newRec->submitdate = time();

        $records = $DB->insert_record('capdmhelpdesk_replies', $newRec);
        break;
}

// Define the $data object to be returned.
$data = new stdClass();

if($records){
    array_push($ret, array('status'=>0));
    $data->return = $ret;
} else {
    array_push($ret, array('status'=>-1));
    $data->return = $ret;
}

echo json_encode($data);
ob_end_flush();
