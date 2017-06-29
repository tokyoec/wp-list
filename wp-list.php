<?php
/**
* @package wp-list
* @version 1.0
*/
/*
Plugin Name: WordPress Site List
Plugin URI: https://github.com/tokyoec/wp-list
Description: 管理画面にWordPressサイト一覧表示する。
Author: Hong Chen
Version: 1.0
Author URI: https://github.com/chen420
*/

// アクションフック
add_action( 'admin_menu', 'wp_list_add_plugin_admin_menu' );

// アクションフックで呼ばれる関数
function wp_list_add_plugin_admin_menu() {
     add_options_page(
          'wp_list', // page_title
          'WordPress Site List', // menu_title
          'administrator', // capability
          'wp-list', // menu_slug
          'wp_list_display_plugin_admin_page' // function
     );
}

class SaneDb
{
    private $_oDb;

    public function __construct(wpdb $oDb)
    {
        $this->_oDb = $oDb;
    }

    public function __get($sField)
    {
        if($sField != '_oDb')
            return $this->_oDb->$sField;
    }

    public function __set($sField, $mValue)
    {
        if($sField != '_oDb')
            $this->_oDb->$sField = $mValue;
    }

    public function __call($sMethod, array $aArgs)
    {
        return call_user_func_array(array($this->_oDb, $sMethod), $aArgs);
    }

    public function getDbName() { return $this->_oDb->dbname;     }
    public function getDbPass() { return $this->_oDb->dbpassword; }
    public function getDbHost() { return $this->_oDb->dbhost;     }
    public function getDbUser() { return $this->_oDb->dbuser; 	}
}

// 設定画面用のHTML
function wp_list_display_plugin_admin_page() {
    wp_list_display_db_list();
    wp_list_display_site_list();
}

function wp_list_display_db_list() {
  	global $sanedb;
  	global $wpdb;
  	$sanedb = new SaneDb($wpdb);

    echo '<div class="wrap">';
    echo '<p>WordPress Site List プラグイン 管理画面</p>';
    echo "[" . $sanedb->getDbHost() . "] db list<br>";;
    echo '</div>';

    echo "<table border=1>";
    echo "<tr>";
    echo "<td> DB Name </td>";
    echo "<td> WP home </td>";
    echo "<td> WP blogname </td>";
    echo "<td> WP blogdescription </td>";
    echo "</tr>";

    $db_host = $sanedb->getDbHost();
    $db_user = $sanedb->getDbUser();
    $db_pass = $sanedb->getDbPass();
    $table_prefix = "wp_";


    // MySQLへ接続する
    $conn = mysqli_connect($db_host, $db_user, $db_pass);

    // データベース情報の取得
    // $rs = mysql_list_dbs($conn);
    $res = mysqli_query($conn, "SHOW DATABASES");

    while ($row = mysqli_fetch_assoc($res)) {

      $db_name = $row['Database'] ;

  	  echo "<tr>";
      echo "<td>$db_name</td>";
      get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, "home");
      get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, "blogname");
      get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, "blogdescription");
      echo "</tr>";
    }
    echo "</table>";

    // MySQLから切断する
    mysqli_close($conn);

    exit(0);
}

wp_list_display_site_list()
{
    $result = array();
		$cmd = "locate wp-includes/version.php";
		exec($cmd, $result);

    echo "<font color=#FF0000>modify within 5 days </font> ";

    echo "<table border=1>";
    echo "<tr>";
    echo "<td> PATH<br>(wp plugin update) </td>";
    echo "<td> VER </td>";
    echo "<td> DBG <br> CHE</td>";
    echo "<td bgcolor=#80C0C0> DB HOST <br> DB NAME</td>";
    echo "<td bgcolor=#80C0C0> DB USER <br> DB PW </td>";
    echo "<td bgcolor=#80C0C0> URL <br> -VHOST- </td>";
    echo "<td bgcolor=#80C0C0> PREFIX <br> WPLANG </td>";
    echo "<td> WRD DEF</td>";
    echo "<td> LMT LOG</td>";
    echo "<td> SUP CHE</td>";
    echo "<td> BUD PRS </td>";
    echo "<td> BB PRS </td>";
    echo "<td> Tiny DB</td>";
    echo "<td> GG PUB</td>";
    echo "<td> UPD CTL</td>";
    echo "<td nowrap> wp-config.php <br> index.php";
    echo "<td nowrap>ME<br>PO<br>UP</td>";
    echo "</tr>";

    foreach ($result as $wpver) {
    		$wproot = substr($wpver, 0, strlen($wpver) - 23);
    		$wpconfig = $wproot . "wp-config.php";
    		if (file_exists($wpconfig) and substr($wproot, 0, 6) == "/home/") {
    				if (!file_exists($wpconfig)) {
    						echo "<tr bgcolor=#995555>";
    				} elseif (file_exists($wproot . "/wpmu-settings.php")) {
    						echo "<tr bgcolor=#00FFFF>";
    				} else {
    						echo "<tr>";
    				}
    				echo "<td><a href = './index.php?op=listwp.php&wp_path=" . $wproot . "'>" . $wproot . "</a></td>";
    				show_wp_ver($wpver);

    				// show_path_exist($wproot . "/wp-admin/.svn");
    				echo "<td>";
    				if (file_exists($wproot . "wp-content/debug.log")) {
    					echo filesize($wproot . "wp-content/debug.log") . "<br>";
    				}
    				show_wp_info($wpconfig);
    				show_plugin_ver($wproot . "/wp-content/plugins/wp-super-cache");
    				show_plugin_ver($wproot . "/wp-content/plugins/wordfence");
    				show_plugin_ver($wproot . "/wp-content/plugins/limit-login-attempts");
    				show_plugin_ver($wproot . "/wp-content/plugins/buddypress");
    				show_plugin_ver($wproot . "/wp-content/plugins/bbpress");
    				show_plugin_ver($wproot . "/wp-content/plugins/tinywebdb-api");
    				show_plugin_ver($wproot . "/wp-content/plugins/google-publisher");
    				show_plugin_ver($wproot . "/wp-content/plugins/update-control");
    				$webroot = str_replace("/wordpress", "", $wproot);
    				echo "<td nowrap>";
    				echo show_file_info($wproot . "wp-config.php");
    				echo "<br>";
    				echo show_file_info($webroot . "index.php");
    				echo "<br>";
    				echo show_file_info($webroot . ".htaccess");
    				echo "</td>";
    				show_htaccess_info($webroot . ".htaccess");
    				echo "</tr>";
    		}
    }
    echo "</table>";
    if(isset($_GET["wp_path"])) {
    		wp_plugin_updata($_GET["wp_path"]);
    }


}

// wp_plugin_updata
// (Using wp tool)
// 1. remove .svn path
// 2. update wp plugin using wp

function wp_plugin_updata($path) {

		$result = array();
		$cmd = "/usr/bin/wp plugin update --all --path='" . $path . "'";
		$cmd2 = "find '" . $path . "' -name .svn -exec /bin/rm -fr {} \;";

		exec( "$cmd2 2>&1 ", $result);
		echo "<p>result info ...<br>\n";
		echo join("<br>", $result);

		echo "Try $cmd as " . get_current_user() . "(" . getmyuid() . "/" . getmygid() . ") ...<br>\n";
		exec( "$cmd 2>&1 ", $result);
		echo "<p>result info ...<br>\n";
		echo join("<br>", $result);
}

// show_file_info
// (red if file new)

function show_file_info($fn) {
		if (time() - filemtime($fn) < 5 * 24 * 3600) {
				echo "<font color=#FF0000>";
				echo date("[F d Y] ", filemtime($fn));
				echo "</font>";
		} else {
				echo date("[F d Y] ", filemtime($fn));
		}
		echo filesize($webroot . $fn);
}

function show_plugin_ver($path) {
		echo "<td>";
		if (file_exists($path)) {
				if (file_exists($path . "/readme.txt")) {
						$sFile = file_get_contents($path . "/readme.txt");
						preg_match('/Stable tag: (.*)/', $sFile, $match1);
						echo $match1[1];
				} elseif (file_exists($path . "/Readme.txt")) {
						$sFile = file_get_contents($path . "/Readme.txt");
						preg_match('/Stable tag: (.*)/', $sFile, $match1);
						echo $match1[1];
				} else {
						echo "ok";
				}
		} else {
				echo " ";
		}
		echo "</td>";
}

function show_wp_ver($v) {
		echo "<td>";
		if (file_exists($v)) {
				$sFile = file_get_contents($v);
				preg_match('/wp_version = (.*)\;/', $sFile, $match1);
				$title = mb_convert_encoding($match1[1], "UTF-8", "gb2312, big5, eucjp-win");
				echo $title;
		}
		echo "</td>";
}

function show_wp_info($wpconfig, $is_xoops) {
		// echo "<td>";
		$sFile = file_get_contents($wpconfig);

		echo "<br>";
		preg_match("/WP_CACHE[\"']\, (.*)\)/", $sFile, $match1);
		$db_host = $match1[1];
		echo "<font color=#009F9F>" . $match1[1] . "</font>";
    echo "</td>";
    echo "<td>";

		preg_match("/table_prefix  = [\"'](.*)[\"']\;/", $sFile, $match1);
		$table_prefix = $match1[1];

		preg_match("/DB_HOST[\"']\, [\"'](.*)[\"']\)/", $sFile, $match1);
		$db_host = $match1[1];
		echo $match1[1];

		preg_match("/DB_NAME[\"']\, [\"'](.*)[\"']/", $sFile, $match1);
		$db_name = $match1[1];
		echo "<br>";
		echo $match1[1];
		echo "</td>";
		if (preg_match("/FORCE_SSL_ADMIN/", $sFile, $match1)) {
				echo "<td bgcolor=#00FF00>";
		} else {
				echo "<td>";
		}
		preg_match("/DB_USER[\"']\, [\"'](.*)[\"']/", $sFile, $match1);
		$db_user = $match1[1];
		echo $match1[1];
		echo "<br>";
		echo "****";
		echo "</td>";
		if (preg_match("/VHOST[\"']\, [\"'](.*)[\"']/", $sFile, $match1)) {
				if ($match1[1] === "no") echo "<td bgcolor=#7FFFD4>";
				else echo "<td bgcolor=#FFB6C1>";
				get_wpmu_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix);
				echo "<br>VHOST -" . $match1[1] . "- ";
		} else if (preg_match("/SUBDOMAIN_INSTALL[\"']\, (.*)\);/", $sFile, $match1)) {
				if ($match1[1] === "true") echo "<td bgcolor=#FF7F50>";
				else echo "<td bgcolor=#FFFF00>";
				get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, "home");
				echo "<br>[";
				get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, "blogname");
				echo "]<br>SUBDOMAIN_ -" . $match1[1] . "- ";
		} else {
				echo "<td>";
				get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, "home");
				echo "<br>[";
				get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, "blogname");
				echo "]";
		}
		echo "</td>";
		echo "<td>";
		echo $table_prefix;
		echo "<br>";
		if (preg_match("/WPLANG[\"']\, [\"'](.*)[\"']/", $sFile, $match1)) {
				echo $match1[1];
		}
		echo "</td>";

}

function get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, $option_name) {
    echo "<td>";
    $db = mysqli_connect($db_host, $db_user, $db_pass) or echo "db open err!";
    mysqli_set_charset($db, 'utf8') or die(mysqli_error($db));
  	mysqli_select_db($db, $db_name) or echo "<font color=#FF0000>db select err!<font>";
  	$result = mysqli_query($db, "SELECT option_value FROM " . $table_prefix . "options WHERE option_name='$option_name'");
  	if ($result) {
  		$myrow = mysqli_fetch_array($result);
  		switch ($option_name) {
  			case "home":   // make a hyper link
  				echo "<a href = " . $myrow['option_value'] . ">";
  				echo $myrow['option_value'] . "</a>";
  			  break;
  			default:
  				echo $myrow['option_value'] ;
  			break;
  		}
  	}
  	// mysqli_free_result($result);
    echo "</td>";
}

function get_wpmu_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix) {
		$db = mysqli_connect($db_host, $db_user, $db_pass) or echo "db open err!";
		if (!mysqli_select_db($db, $db_name)) or echo "<font color=#FF0000>db select err!<font>";
		$result = mysqli_query($db, "SELECT domain, path FROM " . $table_prefix . "site ");
		if ($result) {
				$myrow = mysqli_fetch_array($result);
				echo "<a href = http://" . $myrow['domain'] . $myrow['path'] . ">";
				echo "http://" . $myrow['domain'] . $myrow['path'] . "</a>";
				mysqli_free_result($result);
		}
}




?>
