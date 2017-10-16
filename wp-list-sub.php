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

function wp_list_display_plugin_search_page() {
    global $locate;

	echo '<div class="wrap">';
	echo '<h1>WordPress DB and Site List Reindex</h1>';
	echo '</div>';

	echo "<br>\n DOCUMENT_ROOT : " . $_SERVER["DOCUMENT_ROOT"] . " HOME : " . $_SERVER["HOME"] . " ... ";
	if (empty($_SERVER["DOCUMENT_ROOT"])) {
        $home = $_SERVER["HOME"];
	} else {
        $home = dirname($_SERVER["DOCUMENT_ROOT"]);
	}

	echo "<br>\n find user $home folders ... ";
	wp_search_directory ($home);
	wp_list_display_site_list($locate);
}


function wp_search_directory($dir = "root_dir/dir")
    {

        global $locate;

        $listDir = array();
        if($handler = opendir($dir)) {
            while (($sub = readdir($handler)) !== FALSE) {
                if ($sub != "." && $sub != ".."
                        && substr($sub, 0, 1) != "."
                        && substr($sub, 0, 4) != "temp"
                        && substr($sub, 0, 5) != "cache"
                        && substr($sub, 0, 6) != "Backup"
                        && substr($sub, 0, 7) != "private"
                        && substr($sub, 0, 8) != "workshop"
                        && $sub != "Maildir"
                        && $sub != "mail_template"
                        && $sub != "tmp"
                        && $sub != "attach"
                        && $sub != "jabber"
                        && $sub != "uploads"
                        && $sub != "logs") {
                    if(is_file($dir."/".$sub)) {
                        $listDir[] = $sub;
                        if ((basename($dir) == 'wp-includes') and ($sub == 'version.php')) {
                                $locate[] = "$dir/$sub";
                        } elseif ((basename($dir) == 'include') and ($sub == 'version.php')) {
                                $locate[] = "$dir/$sub";
                        }
                    }elseif(is_dir($dir."/".$sub) && is_readable($dir."/".$sub)){
                        $listDir[$sub] = wp_search_directory($dir."/".$sub);
                    }
                }
            }
            closedir($handler);
        }
        return $listDir;
    }
