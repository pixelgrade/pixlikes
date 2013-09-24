<?php return array
	(
		'type' => 'group',
//		'label' => 'Post Types',

		// Custom field settings
		// ---------------------

		'options' => array
			(
				'enable_portfolio' => array
					(
						'label' => 'Enable Portfolio',
						'default' => true,
						'type' => 'switch',
					), /* ALL THESE PREFIXED WITH PORTFOLIO SHOULD BE KIDS!! **/

				'enable_portfolio_group' => array
					(
						'type' => 'group',
						'show_on' => 'enable_portfolio',
						'options' => array
							(
								'portfolio_single_item_label' => array
									(
										'label' => 'Single Item Label',
										'desc' => 'Here you can change the singular label.The default is "Project"',
										'default' => 'Project',
										'type' => 'text',
									),
								'portfolio_multiple_items_label' => array
									(
										'label' => 'Multiple Items Label (plural)',
										'desc' => 'Here you can change the plural label.The default is "Projects"',
										'default' => 'Projects',
										'type' => 'text',
									),
								'portfolio_change_single_item_slug' => array
									(
										'label' => 'Change Single Item Slug',
										'desc' => 'Do you want to rewrite the single portfolio item slug?',
										'default' => true,
										'type' => 'switch',
									),
								'portfolio_change_single_item_slug_group' => array
									(
										'type' => 'group',
										'show_on' => 'portfolio_change_single_item_slug',
										'options' => array
											(
												'portfolio_new_single_item_slug' => array
													(
														'label' => 'New Single Item Slug',
														'desc' => 'Change the single portfolio item slug as you need it.',
														'default' => 'project',
														'type' => 'text',

														// extra pixtype-group options
														'pixtype-group-example' => 'from your.domain.com/portfolio/item1 in your.domain.com/new-slug/item1',
														'pixtype-group-note' => 'After you change this you need to go and save the permalinks to flush them.'
													),
											),
									),
						),
				),
				'enable_gallery' => array
					(
						'label' => 'Enable Gallery',
						'default' => true,
						'type' => 'switch',
					),
			)
	); # config