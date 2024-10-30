<?php
/**
 * Plugin Name: Ma-Visioconference
 * Plugin URI: http://www.ma-visioconference.fr
 * Description:  Ma-Visioconference for wordpress.
 * Version: 1.0.0.2
 * Author: DiVA-Cloud
 * Author URI: http://www.diva-cloud.com
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

require_once(dirname( __FILE__ )."/includes/Ma-Visioconference-API.class.php");
require_once(dirname( __FILE__ )."/includes/Ma-Visioconference.class.php");

$ma_visioconference = new Ma_Visioconference();
add_action('admin_menu', array($ma_visioconference, 'admin_menu'));
add_action('init', array($ma_visioconference, 'register_shortcodes'));
add_action('admin_head', array($ma_visioconference, 'register_mce'));
add_action('plugins_loaded', array($ma_visioconference, 'load_translation_files'));

foreach ( array('post.php','post-new.php') as $hook ) {
  add_action( "admin_head-$hook", array($ma_visioconference, 'admin_head' ));
}

?>
