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
 * Plugin Name: PixLikes
 * Plugin URI:  http://pixelgrade.com
 * Description: Likes ajax sistem
 * Version:     1.0.0
 * Author:      pixelgrade
 * Author URI:  http://pixelgrade.com
 * Text Domain: pixlikes
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-pixlikes.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
//register_activation_hook( __FILE__, array( 'PixLikes', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'PixLikes', 'deactivate' ) );

global $pixlikes;
$pixlikes = PixLikes::get_instance();

function pixlikes() {
	global $pixlikes;
	echo $pixlikes->display_pixlikes();
}


function display_pixlikes( $class = '' ) {
	global $pixlikes;
	echo $pixlikes->display_likes_number($class);
}