<?php
/*
Plugin Name: CSV Viewer
Plugin URI: http://www.jansen-cws.nl
Description: Reads CSV-files en shows it on the WordPress-page
Version: 1.0.2
Author: Jansen CWS
Author URI: http://www.jansen-cws.nl

  Copyright 2010  Jansen CWS  (email : info@jansen-cws.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$basedir = str_replace(DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'csv-viewer', '', dirname(__FILE__));
define(CSV_BASEDIR, $basedir.DIRECTORY_SEPARATOR);
define(CSV_PLUGIN_BASEDIR, dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once(CSV_PLUGIN_BASEDIR.'config.php');
require_once(CSV_PLUGIN_BASEDIR.'install.php');
require_once(CSV_PLUGIN_BASEDIR.'uninstall.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/csv_viewer.class.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/csv_uploader.class.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/csv_filelist.class.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/csv_settings.class.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/csv_style.class.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/csv_upload_images.class.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/configpage_templates.class.php');
require_once(CSV_PLUGIN_BASEDIR.'includes/classes/configpage_functions.class.php');

//Functie aan Wordpress menu toevoegen
add_action('admin_menu', 'cwsCsvViewerMenu');
add_action('admin_init', 'cwsGetSheet');
add_shortcode('csv', 'cwsDetectTag');
load_plugin_textdomain('csv_trans','wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'csv-viewer'.DIRECTORY_SEPARATOR.'translations');
//Menu van de plugin
function cwsCsvViewerMenu() {
	add_options_page('CSV Viewer options', 'CSV Viewer', 'manage_options', 'csv-viewer', 'cwsCsvViewerOptions');
}

function cwsCsvViewerOptions() {
	//Standaard acties worden uitgevoerd
	$get_posts = new CwsConfigpageFunctions();
	//Controleren op posts
	$get_posts->cwsGetPost();
}

function cwsDetectTag($atts){
	extract(shortcode_atts(array(
		'id' => ''
	), $atts));

	global $wpdb;
	$results = $wpdb->get_results("SELECT `name` FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `id` = '{$id}'");
	if(empty($results)){
		return 'Dit is een ongeldige Id';
	}

	$templateclass = new CwsConfigpageFunctions();
	if (($document = $templateclass->cwsReturnDocument($results[0]->name)) === false){
		return 'Dit is een ongeldige Id';
	}
	
	return $document;
}

function cwsGetSheet(){
	$cssfiles = array('style.css', 'colorbox.css', 'default.css');
	for ($i=0; $i<count($cssfiles); $i++){
		$myStyleUrl = get_option('siteurl').'/wp-content/plugins/csv-viewer/css/'.$cssfiles[$i];
		$myStyleFile = CSV_PLUGIN_BASEDIR.'css'.DIRECTORY_SEPARATOR.$cssfiles[$i];
		if ( file_exists($myStyleFile) ) {
			wp_register_style('myStyleSheets'.$i, $myStyleUrl);
			wp_enqueue_style('myStyleSheets'.$i);
		}
	}
}

wp_enqueue_script('jquery');
wp_enqueue_script('colorbox', get_option('siteurl').'/wp-content/plugins/csv-viewer/js/jquery.colorbox-min.js', array(), '1.0', true);
wp_enqueue_script('csvviewer', get_option('siteurl').'/wp-content/plugins/csv-viewer/js/csvviewer.js', array(), '1.0', true);
wp_enqueue_script('metadata', get_option('siteurl').'/wp-content/plugins/csv-viewer/js/multiupload/jquery.MetaData.js', array(), '1.0', true);
wp_enqueue_script('multifile', get_option('siteurl').'/wp-content/plugins/csv-viewer/js/multiupload/jquery.MultiFile.js', array(), '1.0', true);
add_action('wp_print_styles', 'cwsGetSheet');