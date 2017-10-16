<?php
/**
* @package wp-list
* @version 1.0
*/
/* 
 * wp-list subpage
 */

$locate = array();
$echodb = array();
$xechodb = array();

function wp_list_display_plugin_locate_page() {
    $result = array();
	$cmd = "locate wp-includes/version.php";
	exec($cmd, $result);
	wp_list_display_site_list($result);

}

