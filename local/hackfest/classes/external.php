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
 * @package    local_hackfest
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_hackfest;

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

/**
 * This is the external API for this plugin.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    function gal_test(){
        return 'gal test worked';
    }

    /**
     * Wrap the core function get_site_info.
     *
     * @return external_function_parameters
     */
    public static function get_site_info_parameters() {
        return \core_webservice_external::get_site_info_parameters();
    }

    /**
     * Expose to AJAX
     * @return boolean
     */
    public static function get_site_info_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Wrap the core function get_site_info.
     */
    public static function get_site_info($serviceshortnames = array()) {
        global $PAGE;
        $PAGE->set_context();
        $renderer = $PAGE->get_renderer('local_hackfest');
        $page = new \local_hackfest\output\index_page();
        return $page->export_for_template($renderer);
    }

    /**
     * Wrap the core function get_site_info.
     *
     * @return external_description
     */
    public static function get_site_info_returns() {
        $result = \core_webservice_external::get_site_info_returns();
        $result->keys['currenttime'] = new external_value(PARAM_RAW, 'the current time');
        return $result;
    }

    // ########################################
    // ########################################
    // gal section
    // ########################################
    // ########################################

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
    * Expose to AJAX
    * @return boolean
    */
    public static function get_replies_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns the list of replies to the supplied message ID.
     *
     * @param int $msgid        Original message id
     * @return array            Array containing warnings and the orig message replies.
     * @since  Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_replies($replyto = 0) {
        global $DB;

        $warnings = array();

        $params = array(
            'replyto' => $replyto,
        );
        $params = self::validate_parameters(self::get_replies_parameters(), $params);

        $result = array();
        $result['replies'] = array();
        $result['replyto'] = $replyto;
        $result['warnings'] = $warnings;

        $user = $DB->get_records_sql('select r.id, replyto, message, from_unixtime(submitdate, \'%D %M %Y %h:%i\') as submitdate, username, case when username is null then \'self\' else concat(firstname, \' \', lastname) end as fullname, case replierid when 0 then \'self\' else \'other\' end as \'originator\' from {capdmhelpdesk_replies} r left join {user} u on r.replierid = u.id where replyto = :replyto order by submitdate desc', array('replyto'=>$replyto));

        foreach ($user as $u) {
            $result['replies'][] = array(
                'replyto' => $u->replyto,
                'message' => $u->message,
                'submitdate' => $u->submitdate,
                'username' => $u->username,
                'originator' => $u->originator,
                'fullname' => $u->fullname,
            );
        }

        return $result;
    }

    /**
     * Describes the get_replies return value.
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
                'warnings' => new external_warnings(),
            )
        );
    }


    // message section
    // gal section

    /**
     * Describes the parameters for get_user_info.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_user_info_parameters() {
        return new external_function_parameters (
            array(
                'userid' => new external_value(PARAM_INT, 'Details only for this user id, empty for current user', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
    * Expose to AJAX
    * @return boolean
    */
    public static function get_user_info_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns the list of badges awarded to a user.
     *
     * @param int $userid       user id
     * @return array array containing warnings and the awarded badges
     * @since  Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_user_info($userid = 0) {
        global $DB, $USER;

        $warnings = array();

        $params = array(
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_user_info_parameters(), $params);



        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        $result = array();
        $result['user'] = array();
        $result['warnings'] = $warnings;

        $user = $DB->get_records('user', array('id'=>$userid));

        foreach ($user as $u) {
            $result['user'][] = array(
                'username' => $u->username,
                'firstname' => $u->firstname,
                'lastname' => $u->lastname,
                'email' => $u->email,
            );
        }

        return $result;
    }

    /**
     * Describes the get_user_badges return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_user_info_returns() {
        return new external_single_structure(
            array(
                'user' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'username' => new external_value(PARAM_TEXT, 'Username.'),
                            'firstname' => new external_value(PARAM_TEXT, 'Firstname.'),
                            'lastname' => new external_value(PARAM_TEXT, 'Lastname.'),
                            'email' => new external_value(PARAM_TEXT, 'Email.'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }


}
