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

?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form action="options-general.php?page=pixlikes" method="post" id="pixlikes_form">
		<?php
		settings_fields('pixlikes');
		do_settings_sections( 'pixlikes' );
		?>
		<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->plugin_slug ); ?>" /></p>
	</form>
</div>
<?php
