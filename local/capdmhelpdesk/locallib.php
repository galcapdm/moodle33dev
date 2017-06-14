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
	function capdmhelpdesk_send_notification($objMsgDetails, $msgID){

		global $DB, $USER, $CFG, $SITE;

		$output = '';

		$enquirer = new stdClass();
		$enquirer->email = $objMsgDetails->email;
		$enquirer->fname = $objMsgDetails->fname;
		$enquirer->lname = $objMsgDetails->lname;
		$enquirer->id = 2;	// junk value to get the email notification to work

		// look up the tutor for the course selected - this could be more than one!
                $admins = $DB->get_field('capdmhelpdesk_config', 'content', array('type'=>'admins'));

		$link = $CFG->wwwroot.'/local/capdmhelpdesk/view.php?menuid=1&thisaction=2&id='.$msgID;
		$body = get_string('newmessagebody','local_capdmhelpdesk', array('link'=>$link));
		if($objMsgDetails->callme == '1'){
			$body .= get_string('callback_requested', 'local_capdmhelpdesk');
		}
		$subject = get_string('newmessagesubject','local_capdmhelpdesk');
		
		$allAdmins = explode('~', $admins);
		// Get the site admin details
		$admin = get_admin();
		
		// now send and email to those uses listed in the admins config list
		foreach($allAdmins as $a){
			$thisAdmin = $DB->get_record('user', array('username'=>$a));
			email_to_user($thisAdmin, $admin, $subject, $body);
		}
		
		// send an acknowledgement to the user
		email_to_user($enquirer, $admin, get_string('newmessagesubject_user','local_capdmhelpdesk'), get_string('newmessagebody_user','local_capdmhelpdesk', array('fname'=>$enquirer->fname, 'site'=>$SITE->fullname)));

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
