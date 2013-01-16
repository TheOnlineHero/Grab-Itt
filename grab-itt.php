<?php
/*
Plugin Name: Grab Itt
Plugin URI: http://wordpress.org/extend/plugins/grab-itt/
Description: Grabs content from another website.

Installation:

1) Install WordPress 3.4.2 or higher

2) Download the following file:

http://downloads.wordpress.org/plugin/grab-itt.zip

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

Version: 1.0
Author: TheOnlineHero - Tom Skroza
License: GPL2
*/
require_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once(dirname(__FILE__).'/simple_html_dom.php');

function grab_itt_activate() {
  global $wpdb;

  $table_name = $wpdb->prefix . "grab_itt";

  $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT, 
  name VARCHAR(255) DEFAULT '',
  url VARCHAR(255) DEFAULT '',
  css_selector VARCHAR(255) DEFAULT '',
  last_cached_date VARCHAR(255),
  cached_content longtext,
  PRIMARY KEY  (id)
  );";

  dbDelta($sql);
}
register_activation_hook( __FILE__, 'grab_itt_activate' );

//call register settings function
add_action( 'admin_init', 'register_grab_itt_settings' );
function register_grab_itt_settings() {
  //register our settings
  @check_grab_itt_dependencies_are_active(
    "Grab Itt", 
    array(
      "Tom M8te" => array("plugin"=>"tom-m8te/tom-m8te.php", "url" => "http://downloads.wordpress.org/plugin/tom-m8te.zip", "version" => "1.1"))
  );
}

add_action('admin_menu', 'register_grab_itt_page');

function register_grab_itt_page() {
   add_menu_page('Grab Itt', 'Grab Itt', 'update_themes', 'grab-itt/grab-itt-list.php', '',  '', 196);
}

add_shortcode( 'grab-itt', 'grab_itt_shortcode' );

function grab_itt_shortcode($atts) {
  $url = $atts['url'];
  $css_selector = $atts['css_selector'];

  $row = tom_get_row("grab_itt", array("url", "css_selector", "last_cached_date", "cached_content"), "url = '$url' AND css_selector = '$css_selector'");
  
  // Check if record does not exist.
  if ($row->url == "" || $row->url == null) { 
    // Create record.
    $content = grab_itt_content_from_url($url, $css_selector);
    tom_insert_record("grab_itt", array("url" => $url, "css_selector" => $css_selector, "last_cached_date" => date("d/m/y"), "cached_content" => $content));
    echo $content;
  } else {
    // Check the last cached date.

    // If cached date has not expired.
    if ($row->last_cached_date == date("d/m/y")) {
      echo $row->cached_content;
    } else {
      // If cached date has expired.
      // Delete existing record and create new one.
      tom_delete_record("grab_itt", "url = $url AND css_selector = $css_selector");
      $content = grab_itt_content_from_url($url, $css_selector);
      tom_insert_record("grab_itt", array("url" => $url, "css_selector" => $css_selector, "last_cached_date" => date("d/m/y"), "cached_content" => $content));
      echo $content;
    }

  }
   
  echo "<div class='content-source'>Content sourced from: $url</div>";
}

function grab_itt_content_from_url($url, $css_selector) {
  // get DOM from URL or file
  $html = file_get_html($url);
  // find all link
  $content = "";
  foreach($html->find($css_selector) as $e) {
    $content .= $e->outertext;
  }
  return $content;
}

function check_grab_itt_dependencies_are_active($plugin_name, $dependencies) {
  $msg_content = "<div class='updated'><p>Sorry for the confusion but you must install and activate ";
  $plugins_array = array();
  $upgrades_array = array();
  define('PLUGINPATH', ABSPATH.'wp-content/plugins');
  foreach ($dependencies as $key => $value) {
    $plugin = get_plugin_data(PLUGINPATH."/".$value["plugin"],true,true);
    $url = $value["url"];
    if (!is_plugin_active($value["plugin"])) {
      array_push($plugins_array, "<a href='$url'>$key</a>");
    } else {
      if (isset($value["version"]) && str_replace(".", "", $plugin["Version"]) < str_replace(".", "", $value["version"])) {
        array_push($upgrades_array, "<a href='$url'>$key</a>");
      }
    }
  }
  $msg_content .= implode(", ", $plugins_array) . " before you can use $plugin_name. Please ";
  $download_plugins_array = array();
  foreach ($dependencies as $key => $value) {
    if (!is_plugin_active($value["plugin"])) {
      $url = $value["url"];
      array_push($download_plugins_array, "<a href='$url'>click here to download $key</a>");
    }
  }
  $msg_content .= implode(", ", $download_plugins_array)."</p></div>";
  if (count($plugins_array) > 0) {
    deactivate_plugins( __FILE__,true);
    echo($msg_content);
  } 

  if (count($upgrades_array) > 0) {
    deactivate_plugins( __FILE__,true);
    echo "<div class='updated'><p>$plugin_name requires the following plugins to be updated: ".implode(", ", $upgrades_array).".</p></div>";
  }
}

?>