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
 * @package    local_capdmhelpdesk
 * @copyright  2017 CAPDM Ltd - www.capdm.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2017070702;              // If version == 0 then module will not be installed
$plugin->requires  = 2017050500;              // Requires this Moodle version
$plugin->cron      = 0;                       // Period for cron to check this module (secs)
$plugin->component = 'local_capdmhelpdesk';   // To check on upgrade, that module sits in correct place
$plugin->maturity  = MATURITY_ALPHA;          // Maturity is ALPHA for this version of the plugin
$plugin->release   = 'v3.3-r1';               // First version for Moodle 3.3