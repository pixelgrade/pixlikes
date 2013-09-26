<?php

return array
	(
		'type' => 'group',
//		'label' => 'Cache Settings',

		// Custom field settings
		// ---------------------

		'options' => array
			(
				'load_likes_with_ajax' => array
					(
						'label' => 'Reload likes number on page load',
						'default' => false,
						'description' > 'This helps you to prevent the likes number to be cached',
						'type' => 'switch',
					),
			)
	); # config