<?php
// WebForm Processor - Example
//
// WebForm Processor is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// WebForm Processor is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License.
// If not, see <http://www.gnu.org/licenses/>.

/**
 *  Version information for WebForm Processor (1.0)
 *
 *  @package    www.github.com/michael-milette/webform
 *  @author     Michael Milette | TNG Consulting Inc. - www.tngconsulting.ca
 *  @copyright  Copyright 2017-2019 TNG Consulting Inc.
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$requiredFields = ['recipient', 'comments'];

$page['lang'] = (empty($_REQUEST['lang']) && $_REQUEST['lang'] == 'fr' ? 'fr' : 'en');
switch ($page['lang']) {
    case 'fr' : // French
        $page['title'] = 'Contactez-nous';
        break;
    default:    // English.
        $page['title'] = 'Contact us';
}
$page['breadcrumbs'] = '<li><a href="index-' . $page['lang'] . '.html">' . $page['title'] . '</a></li>';
        
require 'contact.php';
