<?php

// custom post types
$post_types = get_post_types( array(
	'public'   => true,
	'_builtin' => false
), 'names' );

$post_types_array = array
	(
		'show_on_post' => array
		(
			'label' => 'Posts',
			'default' => false,
			'type' => 'switch',
		), /* ALL THESE PREFIXED WITH PORTFOLIO SHOULD BE KIDS!! **/
		'show_on_page' => array
		(
			'label' => 'Pages',
			'default' => false,
			'type' => 'switch',
		),
	);

foreach ( $post_types as $post_type ) {

	$add_to_string = 'show_on_'.$post_type;
	$post_types_array[$add_to_string] = array
		(
			'label' => $post_type,
			'default' => false,
			'type' => 'switch',
		);
}

$settings =  array
	(
		'type' => 'postbox',
		'label' => 'Show on',


		// Custom field settings
		// ---------------------

		'options' => array
			(
				'show_on_hompage' => array
					(
						'label' => 'Home page',
						'default' => false,
						'type' => 'switch',
					),
				'show_on_archive' => array
					(
						'label' => 'Archives like blog, categories, search page',
						'default' => false,
						'type' => 'switch',
					),
			),
	); # config


$settings['options'] = array_merge($settings['options'], $post_types_array);

return $settings;
