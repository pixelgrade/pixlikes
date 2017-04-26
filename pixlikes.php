<?php
/**
Plugin Name: PixLikes
Plugin URI:  http://pixelgrade.com
Description: Likes ajax sistem
Version:     1.1.3
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


/* Automagical updates */
function wupdates_check_v75R3( $transient ) {
	// The plugin basename (directory/main-file.php)
	$plugin = plugin_basename( __FILE__ );

	// Nothing to do here if the checked transient entry is empty or if we have already checked
	if ( empty( $transient->checked ) || empty( $transient->checked[ $plugin ] ) || ! empty( $transient->response[ $plugin ] ) || ! empty( $transient->no_update[ $plugin ] ) ) {
		return $transient;
	}

	// Let's start gathering data about the plugin
	// the plugin directory name
	$slug = dirname( $plugin );
	// Then WordPress version
	include( ABSPATH . WPINC . '/version.php' );
	$http_args = array (
		'body' => array(
			'slug' => $slug,
			'plugin' => $plugin,
			'url' => home_url(), //the site's home URL
			'version' => 0,
			'locale' => get_locale(),
			'phpv' => phpversion(),
			'data' => null, //no optional data is sent by default
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
	);

	// If the plugin has been checked for updates before, get the checked version
	if ( ! empty( $transient->checked[ $plugin ] ) ) {
		$http_args['body']['version'] = $transient->checked[ $plugin ];
	}

	// Use this filter to add optional data to send
	// Make sure you return an associative array - do not encode it in any way
	$optional_data = apply_filters( 'wupdates_call_data_request', $http_args['body']['data'], $slug, $http_args['body']['version'] );

	// Encrypting optional data with private key, just to keep your data a little safer
	// You should not edit the code bellow
	$optional_data = json_encode( $optional_data );
	$w=array();$re="";$s=array();$sa=md5('461a0fd7eeae24c1091fdddb62d36f4b71ca6d48');
	$l=strlen($sa);$d=$optional_data;$ii=-1;
	while(++$ii<256){$w[$ii]=ord(substr($sa,(($ii%$l)+1),1));$s[$ii]=$ii;} $ii=-1;$j=0;
	while(++$ii<256){$j=($j+$w[$ii]+$s[$ii])%255;$t=$s[$j];$s[$ii]=$s[$j];$s[$j]=$t;}
	$l=strlen($d);$ii=-1;$j=0;$k=0;
	while(++$ii<$l){$j=($j+1)%256;$k=($k+$s[$j])%255;$t=$w[$j];$s[$j]=$s[$k];$s[$k]=$t;
		$x=$s[(($s[$j]+$s[$k])%255)];$re.=chr(ord($d[$ii])^$x);}
	$optional_data=bin2hex($re);

	// Save the encrypted optional data so it can be sent to the updates server
	$http_args['body']['data'] = $optional_data;

	// Check for an available update
	$url = $http_url = set_url_scheme( 'https://wupdates.com/wp-json/wup/v1/plugins/check_version/v75R3', 'http' );
	if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$raw_response = wp_remote_post( $url, $http_args );
	if ( $ssl && is_wp_error( $raw_response ) ) {
		$raw_response = wp_remote_post( $http_url, $http_args );
	}
	// We stop in case we haven't received a proper response
	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
		return $transient;
	}

	$response = (array) json_decode($raw_response['body']);
	if ( ! empty( $response ) ) {
		// You can use this action to show notifications or take other action
		do_action( 'wupdates_before_response', $response, $transient );
		if ( isset( $response['allow_update'] ) && $response['allow_update'] && isset( $response['transient'] ) ) {
			$transient->response[ $plugin ] = (object) $response['transient'];
		} else {
			//it seems we don't have an update available - remember that
			$transient->no_update[ $plugin ] = (object) array(
				'slug' => $slug,
				'plugin' => $plugin,
				'new_version' => ! empty( $response['version'] ) ? $response['version'] : '0.0.1',
			);
		}
		do_action( 'wupdates_after_response', $response, $transient );
	}

	return $transient;
}
add_filter( 'pre_set_site_transient_update_plugins', 'wupdates_check_v75R3' );

function wupdates_add_id_v75R3( $ids = array() ) {
	$slug = plugin_basename( __FILE__ );
	$ids[ $slug ] = array( 'id' => 'v75R3', 'type' => 'plugin', );

	return $ids;
}
add_filter( 'wupdates_gather_ids', 'wupdates_add_id_v75R3', 10, 1 );