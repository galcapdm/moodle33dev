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
 *
 * @package    local_capdmhelpdesk
 * @copyright  2017 CAPDM Ltd - www.capdm.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once(__DIR__ . '/../../config.php');

if (empty($userid)) {
    if (!isloggedin()) {
        require_login();
    }
    $userid = $USER->id;
}

$PAGE->set_context(context_system::instance());
//$PAGE->set_pagelayout('application');

// Set up the page.
$url = new moodle_url("/local/capdmhelpdesk/view.php", array());

$pagetitle = get_string('capdmhelpdeskname', 'local_capdmhelpdesk');

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);

$output = $PAGE->get_renderer('local_capdmhelpdesk');

echo $output->header();

$helpdeskcontrol = new \local_capdmhelpdesk\output\helpdesk_control();
echo $output->render($helpdeskcontrol);

$params = [];
$PAGE->requires->js_call_amd('local_capdmhelpdesk/capdmhelpdesk', 'init', $params);

echo $output->footer();