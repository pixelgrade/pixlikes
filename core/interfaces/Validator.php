<?php defined('ABSPATH') or die;

/* This file is property of Pixel Grade Media. You may NOT copy, or redistribute
 * it. Please see the license that came with your copy for more information.
 */

/**
 * @package    pixlikes
 * @category   core
 * @author     Pixel Grade Team
 * @copyright  (c) 2013, Pixel Grade Media
 */
interface PixlikesValidator {

	/**
	 * @return array errors
	 */
	function validate($input);

	/**
	 * @param string rule
	 * @return string error message
	 */
	function error_message($rule);

} # interface
