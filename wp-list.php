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
 
// 設定画面用のHTML
function wp_list_display_plugin_admin_page() {
     echo '<div class="wrap">';
     echo '<p>Hello_Worldプラグインの管理画面</p>';
     echo '</div>';
}

?>
