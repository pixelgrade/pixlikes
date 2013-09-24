<?php return array
	(
		'cleanup' => array
			(
				'switch' => array('switch_not_available'),
			),

		'checks' => array
			(
				'counter' => array('is_numeric', 'not_empty'),
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
						// callbacks to run post update
					),
			),

		'errors' => array
			(
				'is_numeric' => __('Numberic value required.', pixlikes::textdomain()),
				'not_empty' => __('Field is required.', pixlikes::textdomain()),
			),

		'callbacks' => array
			(
			// cleanup callbacks
				'switch_not_available' => 'pixlikes_cleanup_switch_not_available',

			// validation callbacks
				'is_numeric' => 'pixlikes_validate_is_numeric',
				'not_empty' => 'pixlikes_validate_not_empty'
			)

	); # config
