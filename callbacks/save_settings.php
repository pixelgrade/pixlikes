<?php defined('ABSPATH') or die;
/**
 * On save action we process all settings for each theme settings we have in db
 *
 * Think about inserting this function in after_theme_switch hook so the settings should be updated on theme switch
 *
 * @param $values
 */

function save_pixlikes_settings( $values ){

//	$options = get_option('pixlikes_settings');
	// save this settings back
//	update_option('pixlikes_settings', $options);

	/** Usually these settings will change slug settings se we need to flush the permalinks */
	flush_rewrite_rules();
}