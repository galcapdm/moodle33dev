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
 * Class containing data for list_domains page
 *
 * @package    local_capdmhelpdesk
 * @copyright  2017 CAPDM Ltd - www.capdm.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_capdmhelpdesk\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;

class helpdesk_control implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
      global $DB, $USER;

        $userid = $USER->id;

        // Define the $data object that is going to be returned plus any common arrays/objects being used.
        $data = new stdClass();
        $messages = array();


        //$records = $DB->get_records_sql('select cat.id, cat.name as cat_name, cat.cat_userid, cat.cat_order as sortorder from {capdmhelpdesk_cat} cat union select id, fullname as cat_name, 0 as cat_userid, sortorder from {course} c where c.id > 1 order by sortorder');
        $records = $DB->get_records_sql('select r.id, r.userid, r.category, r.subject, cat.cat_name, r.message, r.submitdate, r.updatedate, r.updateby, r.status, r.readflag,
                                        u.firstname, u.lastname
                                        from {capdmhelpdesk_requests} r
                                        inner join (
                                        select cat.id, cat.name as cat_name, cat.cat_userid, cat.cat_order as sortorder
                                        from {capdmhelpdesk_cat} cat
                                        union
                                        select id, fullname as cat_name, 0 as cat_userid, sortorder from {course} c where c.id > 1 order by sortorder
                                        ) cat on r.category = cat.id
                                        left join {user} u on r.updateby = u.id
                                        where userid = :userid order by submitdate desc', array('userid'=>$USER->id));

        $cats = array();
        $cat = array();

        // Build an array of arrays representing the records returned from the query.
        foreach($records as $r){
            $cat['id'] = $r->id;
            $cat['value'] = $r->cat_name;
            array_push($cats, $cat);
        }

        // Get the list of helpdesk requests for the supplied userid.
        //$records = $DB->get_records_sql('select * from {capdmhelpdesk_requests} where userid = :userid order by submitdate desc', array('userid' => $userid));
        $records = $DB->get_records_sql('select r.id, r.userid, r.category, r.subject, cat.cat_name, r.message, r.submitdate, case r.updatedate when 0 then \'NA\' else r.updatedate end as updatedate, r.updateby, r.status, r.readflag,
                                        u.firstname, u.lastname
                                        from {capdmhelpdesk_requests} r
                                        inner join (
                                        select cat.id, cat.name as cat_name, cat.cat_userid, cat.cat_order as sortorder
                                        from {capdmhelpdesk_cat} cat
                                        union
                                        select id, fullname as cat_name, 0 as cat_userid, sortorder from {course} c where c.id > 1 order by sortorder
                                        ) cat on r.category = cat.id
                                        left join {user} u on r.updateby = u.id
                                        where userid = :userid order by submitdate desc', array('userid'=>$USER->id));


        // Get some stats for the helpdesk for the supplied userid.
        $stats = $DB->get_records_sql('select 1 as id, userid, \'open\' as status, count(id) as totalStatus from {capdmhelpdesk_requests} where userid = :userid1 and status = 0 group by status union select 2 as id, userid,  \'closed\' as status, count(id) as totalStatus from {capdmhelpdesk_requests} where userid = :userid2 and status = 1 group by status union select 3 as id, userid, \'unread\' as status, count(id) as totalStatus from {capdmhelpdesk_requests} where userid = :userid3 and readflag = 0 group by readflag', array('userid1' => $userid, 'userid2' => $userid, 'userid3' => $userid));

        foreach($stats as $s){
            switch($s->status){
                case 'open':
                    $open = $s->totalstatus;
                    break;
                case 'closed':
                    $closed = $s->totalstatus;
                    break;
                case 'unread':
                    $unread = $s->totalstatus;
                    break;
            }
        }

        $message = array();

        // Build an array of arrays representing the records returned from the query.
        if($records){
            $updated = false;
            // Now loop through the records to build the data->messages array.
            foreach($records as $r){
                $dateSub = date(DATE_RFC2822, $r->submitdate);

                if($r->updatedate === 'NA'){
                    $message['id'] = $r->id;
                    $message['category'] = $r->category;
                    $message['subject'] = $r->subject;
                    $message['message'] = $r->message;
                    $message['status'] = $r->status;
                    $message['readflag'] = $r->readflag;
                    $message['submitdate'] = $dateSub;
                } else {
                    $dateUp = date(DATE_RFC2822, $r->updatedate);
                    $message['id'] = $r->id;
                    $message['category'] = $r->category;
                    $message['subject'] = $r->subject;
                    $message['message'] = $r->message;
                    $message['status'] = $r->status;
                    $message['readflag'] = $r->readflag;
                    $message['submitdate'] = $dateSub;
                    $message['updatedate'] = $dateUp;
                    $message['firstname'] = $r->firstname;
                    $message['lastname'] = $r->lastname;
                }
                array_push($messages, $message);
                // Need to unset the array as not always the same values being set.
                // If you don't unset then the next record keeps the last value.
                unset($message);
            }

        }

        // Set the value for various items sent to the template.
        $data->cats = $cats;
        $data->messages = $messages;
        $data->userid = $USER->id;
        $data->open = $open;
        $data->closed = $closed;
        $data->unread = $unread;

        return $data;
    }
}