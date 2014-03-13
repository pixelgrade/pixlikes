<?php defined('ABSPATH') or die;

	function pixlikes_validate_not_empty($fieldvalue, $processor) {
		return ! empty($fieldvalue);
	}
