<?php

return array
	(
		'type' => 'postbox',
		'label' => __('Cache Settings', pixlikes::textdomain() ),

		// Custom field settings
		// ---------------------

		'options' => array
			(
				'load_likes_with_ajax' => array
					(
						'label' => __('Reload likes number on page load', pixlikes::textdomain() ),
						'default' => false,
						'description' > __('This helps you to prevent the likes number to be cached', pixlikes::textdomain() ),
						'type' => 'switch',
					),
			)
	); # config