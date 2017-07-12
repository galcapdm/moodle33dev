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
 * Internal library of functions for module capdmhelpdesk
 *
 * All the capdmhelpdesk specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    local
 * @subpackage capdmhelpdesk
 * @copyright  2017 CAPDM Ltd - www.capdm.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

	/**
	*	This function sends notification emails to relevant users on the submission, update or otherwise of a ticket
	*
	*	@param $msg_details = an object of info to use for sending the message
	*	@return string
	*/
	function capdmhelpdesk_send_notification($objRecipient, $msgID){

		global $DB, $USER, $CFG, $SITE;

		$output = '';

		$enquirer = new stdClass();
		$enquirer->email = $objRecipient->email;
		$enquirer->firstname = $objRecipient->firstname;
		$enquirer->lastname = $objRecipient->lastname;
                $enquirer->username = 'admin from the code';
		$enquirer->id = 2;	// junk value to get the email notification to work

                $sender = core_user::get_support_user();

		//$link = $CFG->wwwroot.'/local/capdmhelpdesk/view.php?menuid=1&thisaction=2&id='.$msgID;
		//$body = get_string('newmessagebody','local_capdmhelpdesk', array('link'=>$link));
		//$subject = get_string('newmessagesubject','local_capdmhelpdesk');

                // now send and email to those uses listed in the admins config list
//		foreach($allAdmins as $a){
			//$thisAdmin = $DB->get_record('user', array('username'=>$a));
			//email_to_user($thisAdmin, $admin, $subject, $body);
		//}

                // Set boolean for notifying admins to false by default.
                $notify_admins = false;

                switch($msgID){
                    case 'new':
                        $subject = get_string('helpdesk_new_subject_user','local_capdmhelpdesk', array('site'=>$SITE->fullname));
                        $msg = get_string('helpdesk_new_message_thanks','local_capdmhelpdesk', array('fname'=>$objRecipient->firstname, 'site'=>$SITE->fullname));
                        // Set admins message.
                        $subject_admins = get_string('helpdesk_new_subject_admin','local_capdmhelpdesk', array('site'=>$SITE->fullname));
                        $msg_admins = get_string('helpdesk_new_message_admin','local_capdmhelpdesk', array('sender'=>$objRecipient->firstname.' '.$objRecipient->lastname, 'site'=>$SITE->fullname, 'subject'=>$objRecipient->subject));
                        // As this is a new message presumably from a student then need to notify admins also.
                        $notify_admins = true;
                        break;
                    case 'reply':
                        $subject = get_string('helpdesk_reply_subject_user','local_capdmhelpdesk', array('site'=>$SITE->fullname));
                        $msg = get_string('helpdesk_reply_message_user','local_capdmhelpdesk', array('fname'=>$objRecipient->firstname, 'site'=>$SITE->fullname));
                        // Check to see who should be notified about this reply.
                        if($objRecipient->notify == 'admin' ){
                            // Need to notify admins also.
                            $notify_admins = true;
                        }
                        break;
                }

                // Hack for now.
                $admins = $sender;

		// Send an acknowledgement to the user.
		email_to_user($objRecipient, $sender, $subject.$objRecipient->subject, $msg);

                // If need to notify admins then do it to all.
                if($notify_admins){
                    email_to_user($admins, $sender, $subject_admins, $msg_admins);
                }

		return true;
	}

	/**
	*	This function displays messages in a formatted box
	*
	*   @param $msg = message string
	*	@return string
	*/
	function capdmhelpdesk_message($msg, $type='alert-info'){

		$output ='';

                // set the bootstrap alert message style for this message
		$type = 'alert '.$type;

		$output .= html_writer::tag('p', $msg, array('class'=>$type));

		return $output;

	}

	/**
	*	This function checks the supplied userid to see if they are defined as a capdmhelpdesk admin
	*
	*   @param $userID = username being checked
	*	@return boolean
	*/
	function capdmhelpdesk_is_admin($username){

		// admin will always get in!
		if($username == 'admin'){
			return true;
		} else {
			global $DB;

			// look up the tutor for the course selected - this could be more than one!
			$admins = $DB->get_field('capdmhelpdesk_config', 'content', array('type'=>'admins'));

			$allAdmins = explode('~', $admins);

			if(in_array($username, $allAdmins)){
				return true;
			}

			return false;
		}

	}
