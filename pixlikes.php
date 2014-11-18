<?php
/**
 *
 * @package   PixLikes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2013 Pixelgrade Media
 *
 * @wordpress-plugin
Plugin Name: PixLikes
Plugin URI:  http://pixelgrade.com
Description: Likes ajax sistem
Version:     1.1.2
Author:      pixelgrade
Author URI:  http://pixelgrade.com
Text Domain: pixlikes
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// ensure EXT is defined
if ( ! defined('EXT')) {
	define('EXT', '.php');
}

require 'core/bootstrap'.EXT;

$config = include 'plugin-config'.EXT;
require_once( plugin_dir_path( __FILE__ ) . 'class-pixlikes.php' );
// set textdomain
pixlikes::settextdomain($config['textdomain']);

// Ensure Test Data
// ----------------

$defaults = include 'plugin-defaults'.EXT;

$current_data = get_option($config['settings-key']);

if ($current_data === false) {
	add_option($config['settings-key'], $defaults);
}
else if (count(array_diff_key($defaults, $current_data)) != 0) {
	$plugindata = array_merge($defaults, $current_data);
	update_option($config['settings-key'], $plugindata);
}
# else: data is available; do nothing

// Load Callbacks
// --------------

$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$callbackpath = $basepath.'callbacks'.DIRECTORY_SEPARATOR;
pixlikes::require_all($callbackpath);

require_once( plugin_dir_path( __FILE__ ) . 'class-pixlikes.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'PixLikesPlugin', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'PixLikesPlugin', 'deactivate' ) );

global $pixlikes_plugin;
$pixlikes_plugin = PixLikesPlugin::get_instance();

function pixlikes() {
	global $pixlikes_plugin;
	echo $pixlikes_plugin->display_pixlikes();
}


function display_pixlikes( $args = array('display_only' => false, 'class' => '' ) ) {
	global $pixlikes_plugin;
	echo $pixlikes_plugin->display_likes_number($args);
}

function get_pixlikes( $postID ) {
	global $pixlikes_plugin;
	return $pixlikes_plugin->get_likes_number($postID);
}

/**
 * <style>
 * .animate-like {
 *	animation: like-animation 1s;
 *  -webkit-animation: like-animation 1s; }
 *</style>
 */
