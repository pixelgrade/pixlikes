<?php defined('ABSPATH') or die;

	$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;

return array
	(
		'plugin-name' => 'pixlikes',

		'settings-key' => 'pixlikes_settings',

		'textdomain' => 'pixlikes_txtd',

		'template-paths' => array
			(
				$basepath.'core/views/form-partials/',
				$basepath.'views/form-partials/',
			),

		'fields' => array
			(
				'general'
					=> include 'settings/general'.EXT,
				'show_on'
					=> include 'settings/show_on'.EXT,
				'cache'
					=> include 'settings/cache'.EXT,
			),

		'processor' => array
			(
				// callback signature: (array $input, PixlikesProcessor $processor)
				'preupdate' => array
				(
					// callbacks to run before update process
					// cleanup and validation has been performed on data
				),
				'postupdate' => array
				(
					'save_settings'
				),
			),

		'cleanup' => array
			(
				'switch' => array('switch_not_available'),
			),

		'checks' => array
			(
				'counter' => array('is_numeric', 'not_empty'),
			),

		'errors' => array
			(
				'not_empty' => __('Invalid Value.', pixlikes::textdomain()),
			),

		'callbacks' => array
			(
				'save_settings' => 'save_pixlikes_settings'
			),

		// shows exception traces on error
		'debug' => true,

	); # config
