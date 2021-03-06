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
 * English strings for capdmhelpdesk
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    local
 * @subpackage capdmhelpdesk
 * @copyright  2017 CAPDM Ltd - www.capdm.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$string['capdmhelpdesk:addinstance'] = 'Add CAPDM Helpdesk';
$string['capdmhelpdesk:admin'] = 'Is a CAPDMHELPDESK administator';
$string['capdmhelpdesk:canuse'] = 'Can use the CAPDM Helpdesk local plugin';
$string['crontask'] = 'Helpdesk admin tasks.';

$string['modulename_help'] = 'Use the capdmhelpdesk local plugin to provide a method for registred site users to make general enquiries as well as requsts for help for a specific course.';
$string['pluginadministration'] = 'CAPDM Helpdesk administration';
$string['pluginname'] = 'CAPDM Helpdesk';
$string['capdmhelpdesk_name'] = 'capdmhelpdesk';

$string['capdmhelpdesk'] = 'capdmhelpdesk';
$string['capdmhelpdeskstatus'] = 'capdmhelpdesk status';

$string['modulename'] = 'capdmhelpdesk';
$string['modulenameplural'] = 'capdmhelpdesks';

$string['capdmhelpdesksettings'] = 'capdmhelpdesk settings';
$string['capdmhelpdeskintro'] = 'capdmhelpdesk Intro';
$string['capdmhelpdeskname'] = 'Helpdesk';
$string['capdmhelpdesknameadmin'] = 'Helpdesk - Admin view';
$string['capdmhelpdesksetup'] = 'Configure CAPDM Helpdesk';

// NEW STUFF
$string['newmessageheader'] = 'New Helpdesk request';
$string['subject'] = 'Subject';
$string['subjecthelp'] = 'Enter a short subject line that best describes your issue';
$string['message'] = 'Message';
$string['messagehelp'] = 'Tell us as much as you can about the issue you are having as this will help us provide you the best advice.';
$string['category'] = 'Category';
$string['categorylabel'] = 'Category: ';
$string['categoryhelp'] = 'Select a suitable category for this help request so that we can direct it to the relevant person.';
$string['newmessage'] = 'New message';
$string['mymessages'] = 'My messages';
$string['nomessagesyet'] = 'You do not have any helpdesk messages yet. When you do they will be listed here.';
$string['nomessagesyetadmin'] = 'Currently there are no open helpdesk messages in your categories that require your attention. When there are they will be listed here.';
$string['addnewmessagehere'] = 'New message';
$string['submitdate'] = '{$a->datesubmitted}';
$string['updatedate'] = '{$a->dateupdated} by {$a->fullname}';
$string['togglenewform'] = 'Show/hide the new message form';
$string['waiting'] = 'Loading&hellip;';
$string['replyformneedinput'] = 'Please enter your reply text in the input box above and then click on the "Reply" button.';
$string['intro'] = 'Use this helpdesk to contact us about any problems you have with using our site. Click on the "plus" icon above to open the form to create a new message. When you submit the form someone will be in touch.

Click on a message to see more details and any replies. Click again to close it.';
$string['introadmin'] = 'You are viewing a list of open helpdesk messages for the categories you are responsible for. These are listed in date order based on the original submission date.

Click on a message to see more details and any replies. Click again to close it.';
$string['replyby'] = '{$a->replier} replied on {$a->replytime}';
$string['loadingreplies'] = 'Please wait&hellip;loading replies';
$string['noreplies'] = 'This message does not have any replies at this time.';
$string['labelorigmessage'] = 'Original message: ';
$string['labelsubmitdate'] = 'Submit date: ';
$string['labelupdatedate'] = 'Updated: ';
$string['labelsubject'] = 'Subject: ';
$string['labelreplies'] = 'Replies: ';
$string['msgid'] = 'Message ID:';
$string['btnshowall'] = 'Show all';
$string['helpdesk_new_subject_admin'] = 'A new helpdesk message has been submitted for you at {$a->site}';
$string['days'] = 'day(s)';
$string['hrs'] = 'hrs';
$string['mins'] = 'min';
$string['msgage'] = 'Message age: ';
$string['togglecatstitle'] = 'Show/hide category selectors.';
$string['togglecompact'] = 'Show/hide message details.';
$string['autocloselabel'] = 'Set to auto close';
$string['autoclose'] = 'Auto close this message?';
$string['autoclose_help'] = 'If you select this option then this message will be closed automatically by the helpdesk cron job (if active). A default value of 24hrs past the update time is set for this action.';
$string['autoclosetitle'] = 'Check/un-check this option for this message to auto close.';
$string['catviewonly'] = 'View messages just for this category.';
$string['hrallmessage'] = 'Show all';
$string['hr4message'] = '< 4 hr';
$string['hr8message'] = '> 4 and < 8 hrs';
$string['hr12message'] = '> 8 and < 12 hrs';
$string['hr24message'] = '> 12 and < 24hrs';
$string['hr25message'] = '> 24 hrs';
$string['btnshowopen'] = 'Show open only ({$a->open})';
$string['btnshowopentitle'] = 'There are {$a->open} open meassages in your helpdesk list. Click here to view just these messages';
$string['btnshowclosed'] = 'Show closed only ({$a->closed})';
$string['btnshowclosedtitle'] = 'There are {$a->totalclosed} closed meassages in your helpdesk. Click here to view just these messages';
$string['nopermisson'] = 'Sorry, but you do not have permission to use this service. Please check with your system administrator for further assistance.';
$string['reload'] = 'Reload messages';
$string['clicktoview'] = 'Click here to view this message and any replies. Click again to close.';
$string['openreplybox'] = 'Click here to show/hide the reply form';
$string['reloading'] = 'Reloading messages&hellip;please wait.';
$string['closemessagebutton'] = 'Update message status to "CLOSED"';
$string['closemessagebuttontitle'] = 'Click here to mark this message closed.';
$string['replymessagebutton'] = 'Reply to this message';
$string['replymessagebuttontitle'] = 'Click here to reply to this message.';
$string['replyformbutton'] = 'Reply';
$string['replyformbuttontitle'] = 'Click here to add your reply.';
$string['newmessageadded'] = 'Your message has been added and the relevant person notified.';
$string['messageclosed'] = 'This message has been closed.';
$string['alreadyclosed'] = 'This message has been closed. To reopen smimply add a reply and it will automatically be re-opened and the relevant person nootified.';
$string['helpdesk_no_admin_set'] = 'NOTE!!! There are no tutors or admins set for this helpdesk category. Please check and amend where necessary.';
$string['sendingnewmessage'] = 'Please wait&hellip;saving your message and sending confirmation emails.';


$string['helpdesk_new_message_thanks'] = 'Dear {$a->fname}

This message is just to confirm your Helpdesk request has been received and the relevant person has been notified. If your request requires an answer you will be notified via email when this is done and you can return to your Helpdesk to view the update.

Regards

{$a->site}';
$string['helpdesk_new_message_thanks_html'] = '<p>Dear {$a->fname}</p>

<p>This message is just to confirm your Helpdesk request has been received and the relevant person has been notified. If your request requires an answer you will be notified via email when this is done and you can return to your Helpdesk to view the update.</p>

<p>Regards</p>

<p>{$a->site}</p>';
$string['helpdesk_new_subject_user'] = 'Confirmation of your Helpdesk request at {$a->site}';

$string['helpdesk_new_message_admin'] = 'Dear Admin/Tutor,

A new helpdesk message at {$a->site} ({$a->sitewww}) has been received and requires your attention.  Please log in and check.

Message ID: {$a->sitewww}/local/capdmhelpdesk/view.php?msgid={$a->newmsgid}
Message from : {$a->sender}
Subject : "{$a->subject}"

Regards

{$a->site}';
$string['helpdesk_new_message_admin_html'] = '<p>Dear Admin/Tutor,</p>

<p>A new helpdesk message at <a href="{$a->sitewww}">{$a->site}</a> has been received and requires your attention.  Please log in and check.</p>

<p>Message ID: {$a->newmsgid}</p>
<p>Message from : {$a->sender}</p>
<p>Subject : "{$a->subject}"</p>

<p>Regards</p>

<p>{$a->site}</p>

<style type="text/css">
p {
    margin: 0 0 1em 0;
}
a {
    font-weight: bold;
    text-decoration: none;
}
</style>';

$string['helpdesk_reply_message_user'] = 'Dear {$a->fname}

There has been an update to your helpdesk message "{$a->subject}". Please log in at {$a->site} to view this message.

Regards

{$a->site}';
$string['helpdesk_reply_subject_user'] = 'A reply has been posted to your helpdesk message at {$a->site}';
$string['helpdesk_reply_message_admin'] = 'Dear Admin/tutor

There has been an update to a helpdesk message you are responsible for managing:-

Message ID: {$a->msgid}
Message subject: "{$a->subject}"

Please log in at "{$a->site}" to view this message in your Helpdesk.

Regards

{$a->site}';
$string['helpdesk_reply_subject_admin'] = 'A reply has been posted to a helpdesk message you manage at {$a->site}';
























$string['helpdesk_new_direct'] = 'Success&hellip;a new message has been saved in the helpdesk for "{$a->user}" and they have been notified via email.';
$string['helpdesk_new_direct_error'] = 'Oops&hellip;there was a problem sending the email notification of a Helpdesk direct message. Please seek assistance from the System Administrator.';
$string['helpdesk_direct_error'] = 'Oops&hellip;there was a problem writing to the database for your direct message. Please seek assistance from the System Admiistrator.';
$string['helpdesk_reply_thanks'] = 'Thank you for your update. The relevant person has been notified via email that there is an update to this Helpdesk message.';


$string['helpdesk_reopen_thanks'] = 'Thank you for your message. A confirmation email has been sent to {$a->email} and if your message requires a reply then you will recieve an email telling you when this has been done.';
$string['helpdesk_reopen_subject'] = 'Confirmation of your Helpdesk request at {$a->site}';
$string['helpdesk_direct_msg_body_user'] = 'Dear {$a->fname}

A new message has been added to your helpdesk list and requires your attention. To read this message then all you need to do is log in to your account at {$a->site} and click on the Helpdesk tab on your My Details home page.

Regards

{$a->sender}';
$string['helpdesk_direct_msg_subject_user'] = 'A new message is waiting for you at {$a->site}';


$string['helpdesk_new_subject'] = 'A Helpdesk message has been posted at {$a->site} and requires your attention';
$string['helpdesk_new_body'] = 'Dear Admin/Tutor,

A new Helpdesk message has been posted that requires your attention. This has been logged against a category that you have responsibility for.  Simply log into {$a->site} and then click on the helpdesk tab to view the latest heldesk requests.

Here is a link to the message:
{$a->link}

Regards

System Administrator';
$string['helpdesk_reply_subject_tutor'] = 'A Helpdesk message at {$a->site} you are an admin for has been udpated';
$string['helpdesk_reply_subject_user'] = 'Your Helpdesk message at {$a->site} has been udpated';
$string['helpdesk_reply_body_user'] = 'Dear {$a->fname},

A Helpdesk message you sumitted has received a reply. Simply log into {$a->site} and then click on the helpdesk tab to view this update.

Regards

{$a->site}
System Administrator';
$string['helpdesk_reply_body'] = 'Dear {$a->fullname},

A Helpdesk message you are an admin for has had a reply posted and requires your attention. Simply log into {$a->site} and then click on the helpdesk tab to view the latest heldesk requests.

Messge Ref ID # - {$a->newRecID}
Message subject - {$a->subject}
Posted by - {$a->origuser}

Regards

{$a->site}
System Administrator';
$string['helpdesk_reopen_body_user'] = 'Dear {$a->fname}

This message is just to confirm your Helpdesk request has been received and the relevant person has been notified. If your request requires an answer then you will be notified via email when this is done and you can return to your Helpdesk tickets to view the update.

Regards

System Administrator';
$string['helpdesk_reopen_subject'] = 'A Helpdesk message has been posted at {$a->site} and requires your attention';
$string['helpdesk_reopen_subject_user'] = 'Confirmation of your Helpdesk request at {$a->site}';
$string['helpdesk_reopen_body'] = 'Dear Admin/Tutor,

A Helpdesk message has been reopened that requires your attention. This has been logged against a category that you have responsibility for.  Simply log into {$a->site} and then click on the helpdesk tab to view the latest heldesk requests.

Here is a link to the message:
{$a->link)

Regards

System Administrator';


$string['helpdesk_reopen_subject'] = 'Confirmation of your Helpdesk request at {$a->site}';
$string['helpdesk_new_body_user'] = 'Dear {$a->fname}

This message is just to confirm your Helpdesk request has been received and the relevant person has been notified. If your request requires an answer then you will be notified via email when this is done and you can return to your Helpdesk tickets to view the update.

Regards

System Administrator';
// capdmhelpdesk.js strings
$string['enterreplytext'] = 'Please enter some text in teh box above for your reply.';
$string['notallowedtoviewid'] = 'The message ID you supplied is invalid or is for a message you are not allowed to access. Please check and try again.';
$string['tour_newmessage_title, local_capdmhelpdesk'] = 'New request';
$string['tour_newmessage_content, local_capdmhelpdesk'] = 'Click here to get the new message form.';
$string['newmesssagetooltip'] = 'Click to add a new message.';
$string['searchforamessage'] = 'Seach for a message by ID or users name or email';
$string['searchtitle'] = 'Enter a message ID, first name, lastname or email in the search box to find messages from that user.';
$string['searchholdertxt'] = 'Search&hellip;';
$string['searchnotfound'] = 'Nothing found. Please amend your search and try again.';