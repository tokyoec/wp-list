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
  	global $sanedb;
  	global $wpdb;
  	$sanedb = new SaneDb($wpdb);

    echo '<div class="wrap">';
    echo '<p>wp_listプラグインの管理画面</p>';
    echo $sanedb->getDbName() . "<br>";;
    echo $sanedb->getDbPass() . "<br>";;
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

function get_wp_option_value($db_host, $db_name, $db_user, $db_pass, $table_prefix, $option_name) {
  echo "<td>";
  $db = mysqli_connect($db_host, $db_user, $db_pass);
  mysqli_set_charset($db, 'utf8') or die(mysqli_error($db));
	if (!$db) {
		echo "db open err!";
		return;
	}
	if (!mysqli_select_db($db, $db_name)) {
		echo "<font color=#FF0000>db select err!<font>";
		return;
	}
	$result = mysqli_query($db, "SELECT option_value FROM " . $table_prefix . "options WHERE option_name='$option_name'");
	if ($result) {
		$myrow = mysqli_fetch_array($result);
		switch ($option_name) {
			case "home":
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


?>
