<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   PixLikes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2013 Pixelgrade Media
 */

$config = include pixlikes::pluginpath().'plugin-config'.EXT;

// invoke processor
$processor = pixlikes::processor($config);
$status = $processor->status();
$errors = $processor->errors(); ?>

<div class="wrap" id="pixlikes_form">

	<div id="icon-options-general" class="icon32"><br></div>

	<h2>Pixlikes</h2>

	<?php if ($processor->ok()): ?>

		<?php if ( ! empty($errors)): ?>
			<br/>
			<p class="update-nag">
				<strong>Unable to save settings.</strong>
				Please check the fields for errors and typos.
			</p>
		<?php endif; ?>

		<?php if ($processor->performed_update()): ?>
			<br/>
			<p class="update-nag">
				Settings have been updated.
			</p>
		<?php endif; ?>

		<?php echo $f = pixlikes::form($config, $processor); ?>

		<?php echo $f->field('general')->render() ?>

		<?php echo $f->field('show_on')->render() ?>

		<?php echo $f->field('cache')->render() ?>

		<button type="submit" class="button button-primary">
			Save Changes
		</button>

		<?php echo $f->endform() ?>

	<?php elseif ($status['state'] == 'error'): ?>

		<h3>Critical Error</h3>

		<p><?php echo $status['message'] ?></p>

	<?php endif; ?>
</div>
