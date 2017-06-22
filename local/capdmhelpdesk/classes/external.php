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
 * This is the external API for this plugin.
 *
 * @package    local_capdmhelpdesk
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_capdmhelpdesk;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/webservice/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;
use external_warnings;
use stdClass;

/**
 * This is the external API for this plugin.
 *
 * @copyright  2017 CAPDM Ltd - www.capdm.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /*
     *  Get message replies - START.
     */

    /**
     * Describes the parameters for get_replies.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_replies_parameters() {
        return new external_function_parameters (
            array(
                'replyto' => new external_value(PARAM_INT, 'The ID of the original message', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
    * Expose to AJAX. This allows this code to be called using AJAX
    * @return boolean
    */
    public static function get_replies_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns the list of replies to the supplied message ID.
     * This is where most of the data logic is performed.
     *
     * @param   int     $msgid          Original message id
     * @return  array                   Array containing warnings and the orig message replies.
     * @since   Moodle 3.1
     * @throws  moodle_exception
     */
    public static function get_replies($replyto = 0) {
        global $DB;

        // Build an array for any warnings.
        $warnings = array();

        // Build an array of the input parameters so they can be checked using
        // get_replies_parameters to ensure they are of the correct type.
        $params = array(
            'replyto' => $replyto,
        );
        $params = self::validate_parameters(self::get_replies_parameters(), $params);

        // Build an array to hold the itmes to be returned to the template.
        $result = array();
        $result['replies'] = array();       // This is an array to hold the replies records.  An array of arrays!
        $result['replyto'] = $replyto;      // The original message ID.
        $result['status'] = $status;        // The status of the current message i.e. open, closed.
        $result['origmessage'] = $status;        // Text of the original message.
        $result['warnings'] = $warnings;    // Any warnings issued.

        $replies = $DB->get_records_sql('select r.id, replyto, r.message, req.message as origmessage, from_unixtime(r.submitdate, \'%D %M %Y %h:%i\') as submitdate, username, case when username is null then \'You\' else concat(firstname, \' \', lastname) end as fullname, case replierid when 0 then \'You\' else \'other\' end as \'originator\', status from {capdmhelpdesk_replies} r left join {user} u on r.replierid = u.id inner join {capdmhelpdesk_requests} req on r.replyto = req.id where replyto = :replyto order by submitdate desc', array('replyto'=>$replyto));

        foreach ($replies as $r) {
            $result['replies'][] = array(
                'replyto' => $r->replyto,
                'message' => $r->message,
                'submitdate' => $r->submitdate,
                'username' => $r->username,
                'originator' => $r->originator,
                'fullname' => $r->fullname,
            );
            $result['status'] = $r->status;
            $result['origmessage'] = $r->origmessage;
        }

        // Now return the result array of values.
        return $result;
    }

    /**
     * Describes the get_replies return value.
     * This checks the data type of the returned
     * values to make sure they are what is expected.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_replies_returns() {
        return new external_single_structure(
            array(
                'replies' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'replyto' => new external_value(PARAM_INT, 'ID of the original message.'),
                            'message' => new external_value(PARAM_TEXT, 'Detail of the reply.'),
                            'submitdate' => new external_value(PARAM_TEXT, 'Date the reply was submitted converted by the MySQL to a date for consistency.'),
                            'username' => new external_value(PARAM_TEXT, 'Username of the replier.'),
                            'originator' => new external_value(PARAM_TEXT, 'Who is the orignator of this message.'),
                            'fullname' => new external_value(PARAM_TEXT, 'Who is the orignator of this message.  Some logic sorted in the SQL statement.'),
                        )
                    )
                ),
                'replyto' => new external_value(PARAM_INT, 'ID of the original message these replies belong to.'),
                'status' => new external_value(PARAM_INT, 'Status of the parent message.'),
                'origmessage' => new external_value(PARAM_TEXT, 'Text of the parent message.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /*
     *  Get message replies - END.
     */

    /*  ############################################################################################
     *  Save new message - START.
     *  ############################################################################################
     */

    /**
     * Describes the parameters for get_replies.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function save_message_parameters() {
        return new external_function_parameters (
            array(
                'userid' => new external_value(PARAM_TEXT, 'The users moodle ID'),
                'category' => new external_value(PARAM_TEXT, 'Category ID for this message'),
                'subject' => new external_value(PARAM_TEXT, 'Subject of the message.'),
                'message' => new external_value(PARAM_TEXT, 'Text of the message.'),
                'updateby' => new external_value(PARAM_TEXT, 'ID of who last updated the message.'),
                'status' => new external_value(PARAM_TEXT, 'Open/close status.'),
                'readflag' => new external_value(PARAM_TEXT, 'Readflag status.'),
                'params' => new external_value(PARAM_TEXT, 'Parameters with this message.'),
            )
        );
    }

    /**
     * Returns the list of replies to the supplied message ID.
     * This is where most of the data logic is performed.
     *
     * @param   int     $msgid          Original message id
     * @return  array                   Array containing warnings and the orig message replies.
     * @since   Moodle 3.1
     * @throws  moodle_exception
     */
    public static function save_message($userid, $category, $subject, $message, $updateby, $status, $readflag, $params) {
        global $DB;

        // Build an array for any warnings.
        $warnings = array();

        // Build an array of the input parameters so they can be checked using
        // get_replies_parameters to ensure they are of the correct type.
        $params = array(
            'userid' => $userid,
            'category' => $category,
            'subject' => $subject,
            'message' => $message,
            'updateby' => $updateby,
            'status' => $status,
            'readflag' => $readflag,
            'params' => $params,
        );
        //$params = self::validate_parameters(self::get_replies_parameters(), $params);


        // This should be an object but using new stdClass() here causes an error.
        // So...built as an array and then cast as an object when submitting to the DB.
        $record = new stdClass();
        $record->userid = $userid;
        $record->category = $category;
        $record->subject = $subject;
        $record->message = $message;
        $record->submitdate = time();
        $record->updatedate = time();
        $record->updateby = $updateby;
        $record->status = 0;
        $record->readflag = 0;
        $record->params = $params;

        // Insert the record into the database table.
        $ret = $DB->insert_record('capdmhelpdesk_requests', $record);

        // Build an array to hold the itmes to be returned to the template.
        $result = array();
        $result['messages'] = array();       // This is an array to hold the replies records.  An array of arrays!
        $result['status'] = $status;        // The status of the current message i.e. open, closed.
        $result['warnings'] = $warnings;    // Any warnings issued.

        $messages = $DB->get_records_sql('select * from {capdmhelpdesk_requests} where userid = :userid order by submitdate desc', array('userid' => $userid));

        foreach ($messages as $m) {
            $dateSub = date(DATE_RFC2822, $m->submitdate);
            $result['messages'][] = array(
                'id' => $m->id,
                'userid' => $m->userid,
                'subject' => $m->subject,
                'message' => $m->message,
                'submitdate' => $dateSub,
                'updatedate' => $m->updatedate,
                'updateby' => $m->updateby,
                'status' => $m->status,
                'readflag' => $m->readflag,
                'params' => $m->params,
            );
            $result['newmsgid'] = $ret;
        }

        // Now return the result array of values.
        return $result;
    }

    /**
     * This checks the data type of the returned
     * values to make sure they are what is expected.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function save_message_returns() {
        return new external_single_structure(
            array(
                'messages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'ID of the message.'),
                            'userid' => new external_value(PARAM_TEXT, 'Userid of the user posting the message.'),
                            'subject' => new external_value(PARAM_TEXT, 'Subject of the message.'),
                            'message' => new external_value(PARAM_TEXT, 'Text of the message.'),
                            'submitdate' => new external_value(PARAM_TEXT, 'Date message was created.'),
                            'updatedate' => new external_value(PARAM_TEXT, 'Date message was updated.'),
                            'updateby' => new external_value(PARAM_TEXT, 'Who last updated the message.'),
                            'status' => new external_value(PARAM_TEXT, 'Message status.'),
                            'readflag' => new external_value(PARAM_TEXT, 'Readflag status.'),
                            'params' => new external_value(PARAM_TEXT, 'Parameters with this message.'),
                        )
                    )
                ),
                'newmsgid' => new external_value(PARAM_INT, 'ID of the newly inserted message.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /*
     *  Save new message - END.
     */


    /*  ############################################################################################
     *  Reloead messages - START.
     *  ############################################################################################
     */

    /**
     * Describes the parameters for get_replies.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function reload_messages_parameters() {
        return new external_function_parameters (
            array(
                'userid' => new external_value(PARAM_INT, 'The users moodle ID'),
            )
        );
    }

    /**
     * Returns the list of messages for supplied message ID.
     * This is where most of the data logic is performed.
     *
     * @param   int     $userid         Moodle ID of the user to get records for.
     * @return  array                   Array containing warnings and the orig message replies.
     * @since   Moodle 3.1
     * @throws  moodle_exception
     */
    public static function reload_messages($userid = 0) {
        global $DB;

        // Build an array for any warnings.
        $warnings = array();

        // Build an array of the input parameters so they can be checked using
        // get_replies_parameters to ensure they are of the correct type.
        $params = array(
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::reload_messages_parameters(), $params);

        // Build an array to hold the itmes to be returned to the template.
        $result = array();
        $result['messages'] = array();       // This is an array to hold the replies records.  An array of arrays!
        $result['status'] = $status;        // The status of the current message i.e. open, closed.
        $result['warnings'] = $warnings;    // Any warnings issued.

        $messages = $DB->get_records_sql('select * from {capdmhelpdesk_requests} where userid = :userid order by submitdate desc', array('userid' => $userid));

        foreach ($messages as $m) {
            $dateSub = date(DATE_RFC2822, $m->submitdate);
            $result['messages'][] = array(
                'id' => $m->id,
                'userid' => $m->userid,
                'subject' => $m->subject,
                'message' => $m->message,
                'submitdate' => $dateSub,
                'updatedate' => $m->updatedate,
                'updateby' => $m->updateby,
                'status' => $m->status,
                'readflag' => $m->readflag,
                'params' => $m->params,
            );
        }

        // Now return the result array of values.
        return $result;
    }

    /**
     * This checks the data type of the returned
     * values to make sure they are what is expected.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function reload_messages_returns() {
        return new external_single_structure(
            array(
                'messages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'ID of the message.'),
                            'userid' => new external_value(PARAM_TEXT, 'Userid of the user posting the message.'),
                            'subject' => new external_value(PARAM_TEXT, 'Subject of the message.'),
                            'message' => new external_value(PARAM_TEXT, 'Text of the message.'),
                            'submitdate' => new external_value(PARAM_TEXT, 'Date message was created.'),
                            'updatedate' => new external_value(PARAM_TEXT, 'Date message was updated.'),
                            'updateby' => new external_value(PARAM_TEXT, 'Who last updated the message.'),
                            'status' => new external_value(PARAM_TEXT, 'Message status.'),
                            'readflag' => new external_value(PARAM_TEXT, 'Readflag status.'),
                            'params' => new external_value(PARAM_TEXT, 'Parameters with this message.'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /*
     *  Reloead messages - END.
     */


}