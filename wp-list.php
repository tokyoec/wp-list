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
}

?>
