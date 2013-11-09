<?php return array
	(
		'type' => 'postbox',
		'label' => 'General Settings',
		'options' => array
			(
				'like_action' => array
					(
						'name' => 'like_action',
						'label' => 'Like action',
						'default' => 'hover',
						'type' => 'select',
						'options' => array
							(
								'click' => 'Click',
								'hover' => 'Hover'
							),

					),

				'hover_time' => array
					(
						'label' => 'Hover delay',
						'default' => 1500,
						'type' => 'text',
					),

				'free_votes' => array(
					'label' => 'Remove the limit of 1 like per post',
					'default' => false,
					'type' => 'switch'
				),
				'edit_votes'=>array(
					'label' => 'Enable Edit Votes',
					'default' => false,
					'type' => 'switch',
				)
			)
	); # config