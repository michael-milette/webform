<?php
// Page generator for WebForm Processor (success and error messages).
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
 *  Version information for WebForm Processor (0.7)
 *
 *  @package    www.github.com/michael-milette/webform
 *  @author     Michael Milette | TNG Consulting Inc. - www.tngconsulting.ca
 *  @copyright  Copyright 2017-2019 TNG Consulting Inc.
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if(!isset($debug)) {
   $debug = false;
}
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Determine language of the page (default = English).

$page['lang'] = (!empty($_GET['lang']) && $_GET['lang'] == 'fr' ? 'fr' : 'en');

// Set some defaults in case information has not yet been set.
$page['title'] = isset($page['title']) ? $page['title'] : '$page[\'title\']';
$page['breadcrumbs'] = isset($page['breadcrumbs']) ? $page['breadcrumbs'] : '<li><a href="#">$page[\'breadcrumbs\']</a></li>';
$page['content'] = isset($page['content']) ? $page['content'] : '<p>$page[\'content\']</p>';
$includes = realpath(__DIR__);

?><!DOCTYPE html><!--[if lt IE 9]>
<html class="no-js lt-ie9" lang="<?php echo $page['lang']; ?>" dir="ltr">
<![endif]--><!--[if gt IE 8]><!-->
<html class="no-js" lang="<?php echo $page['lang']; ?>" dir="ltr">
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <!-- Web Experience Toolkit (WET) / Boîte à outils de l'expérience Web (BOEW)
        wet-boew.github.io/wet-boew/License-en.html / wet-boew.github.io/wet-boew/Licence-fr.html -->
    <title><?php 
        echo $page['title']. ' - ' . ($page['lang'] == 'fr' ? "Ministère des Femmes et de l'Égalité des genres Canada" : "Women and Gender Equality Canada");
    ?></title>
    <meta content="width=device-width,initial-scale=1" name="viewport">
    <meta name="description" content="<?php echo $page['title']; ?>">
    <?php 
        require "$includes/head-${page['lang']}.html";
        // Insert any extra code you might need such as HTML, CSS or JavaScript into the header.
        if (!empty($page['extraheader'])) {
            echo $page['extraheader'] . PHP_EOL;
        }
    ?>
</head>
<body class="secondary" vocab="http://schema.org/" typeof="WebPage">
    <?php require "$includes/header-${page['lang']}.html"; ?>
    <header>
        <nav id="wb-bc" property="breadcrumb">
            <h2><?php echo ($page['lang'] == 'fr' ? 'Vous êtes ici&nbsp;' : 'You are here'); ?>:</h2>
            <div class="container">
                <ol class="breadcrumb">
                    <?php require "$includes/home-${page['lang']}.html"; ?>
                    <?php echo $page['breadcrumbs']; ?>
                    <li><?php echo $page['title']; ?></li>
                </ol>
            </div>
        </nav>
    </header>
    <main property="mainContentOfPage" class="container">
        <?php require "$includes/alert-${page['lang']}.html"; ?>
        <h1 property="name" id="wb-cont"><?php echo $page['title']; ?></h1>

        <?php echo $page['content']; ?>

        <div class="pagedetails">
            <?php if(empty($page['hideshare'])) require "$includes/share-{$page['lang']}.html"; ?>
            <dl id="wb-dtmd">
                <dt><?php echo ($page['lang'] == 'fr' ? 'Date de modification&#160;' : 'Date modified'); ?>:&#32;</dt>
                <dd><time property="dateModified"><?php echo date("Y-m-d", filemtime($_SERVER['SCRIPT_FILENAME'])); ?></time></dd>
            </dl>
        </div>
    </main>
    <?php // Display footer.
        require "$includes/footer-${page['lang']}.html";
        // Insert any extra code you might need such as HTML, CSS or JavaScript into the header.
        if (!empty($page['extrafooter'])) {
            echo $page['extrafooter'] . PHP_EOL;
        }
    ?>
</body>
</html>
