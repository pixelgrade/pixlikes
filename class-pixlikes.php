<?php
/**
 * PixLikes.
 *
 * @package   PixLikes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2013 Pixelgrade
 */

/**
 * Plugin class.
 *
 * @package PixLikes
 * @author    Pixelgrade <contact@pixelgrade.com>
 */
class PixLikes {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @const   string
	 */
	const VERSION = '1.0.0';
	protected $version = '1.0.0';
	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'pixlikes';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		 add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		 $plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'pixlikes.php' );
		 add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Load admin style sheet and JavaScript.
//		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
//		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		/// think about including this in add_like_box_after_content() so the script fille to be included only when is needed
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// when the user is publishing a post let's ensure he is creating a default likes meta
		add_action( 'publish_post', array( $this, 'init_meta_likes' ) );

		// prepare the plugin settings page
		add_filter( 'admin_init', array( $this, 'init_settings_page' ) );

		// prepend the like box after the content
		add_filter('the_content', array(&$this, 'add_like_box_after_content'));

		add_action('wp_ajax_pixlikes', array(&$this, 'ajax_callback'));
		add_action('wp_ajax_nopriv_pixlikes', array(&$this, 'ajax_callback'));

		// prepend the display_likes after the excerpt
		//add_filter('the_excerpt', array(&$this, 'the_content'));

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$options = $this->get_settings();
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
		$nonce = wp_create_nonce( 'pixlikes' );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'locals',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nounce' => $nonce,
				'load_likes_with_ajax' => $options['load_likes_with_ajax'],
				'already_voted_msg' => __("You already voted!")
			)
		);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'PixLikes', $this->plugin_slug ),
			__( 'PixLikes', $this->plugin_slug ),
			'update_core',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'plugins.php?page=pixlikes' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Add "_pixlikes" meta key for each post with value 0.
	 *
	 * @param    int    $post_id    The id of the post which should get the meta key
	 */
	public function  init_meta_likes ( $post_id ){
		if ( empty($post_id) || !is_numeric($post_id) ) {
			return;
		}
		add_post_meta($post_id, '_pixlikes', '0', true );
	}

	/**
	 * Register PixLikes Settings page
	 */
	public function init_settings_page() {

		// register our settings under "pixlikes" group
		register_setting( 'pixlikes', 'pixlikes_settings' );
		add_settings_section( 'pixlikes', '', array(&$this, 'add_settings_section_header'), 'pixlikes' );

		add_settings_field( 'show_on', __( 'Where to show the like button ? ', $this->plugin_slug ), array(&$this, 'setting_show_on'), 'pixlikes', 'pixlikes' );
		add_settings_field( 'load_likes_with_ajax', __( 'Reload likes number on page load', $this->plugin_slug ), array(&$this, 'setting_load_likes_with_ajax'), 'pixlikes', 'pixlikes' );
	}

	/**
	 * Create a presentation header.
	 * This callback is required by add_settings_section() function.
	 */
	public function add_settings_section_header(){
		echo '<h3>';
		_e('Wasup, this is my presentation header and it is required');
		echo '</h3>';
	}

	/*
	 * Now we need to create a callback for each setting
	 */
	public function setting_show_on() {
		$options = get_option( 'pixlikes_settings' );
		if( !isset($options['show_on_post']) ) $options['show_on_post'] = '0';
		if( !isset($options['show_on_page']) ) $options['show_on_page'] = '0';
		if( !isset($options['show_on_home']) ) $options['show_on_home'] = '0';
		if( !isset($options['show_on_archives']) ) $options['show_on_archives'] = '0';
		// build in posts types
		echo '<div><h3>'. __( 'Default Post Types', $this->plugin_slug ) .'</h3>';
		echo '<fieldset>'.
				'<input type="checkbox" name="pixlikes_settings[show_on_post]" value="'. (($options['show_on_post']) ? '1' : '0') .'"'. (($options['show_on_post']) ? ' checked="checked"' : '') .'/>'.
				'<label for="pixlikes_settings[show_on_post]">post</label>'.
			'</fieldset>';

		echo '<fieldset>'.
			'<input type="checkbox" name="pixlikes_settings[show_on_page]" value="'. (($options['show_on_page']) ? '1' : '0') .'"'. (($options['show_on_page']) ? ' checked="checked"' : '') .'/>'.
			'<label for="pixlikes_settings[show_on_page]">page</label>'.
			'</fieldset>';
		echo '</div><div><h3>'.__( 'Custom Post Types', $this->plugin_slug ) .'</h3>';
		// custom post types
		$post_types = get_post_types( array(
			'public'   => true,
			'_builtin' => false
		), 'names' );

		foreach ( $post_types as $post_type ) {

			$add_to_string = 'show_on_'.$post_type;
			if( !isset($options[$add_to_string]) ) $options[$add_to_string] = '0';

			echo '<fieldset>'.
				'<input type="checkbox" name="pixlikes_settings['. $add_to_string .']" value="'. (($options[$add_to_string]) ? '1' : '0') .'"'. (($options[$add_to_string]) ? ' checked="checked"' : '') .'/>'.
				'<label for="pixlikes_settings['. $add_to_string .']">'. $post_type .'</label>'.
				'</fieldset>';
		}

		echo '</div><div><h3>'.__( 'Other places', $this->plugin_slug ) .'</h3>';

		echo '<fieldset>'.
			'<input type="checkbox" name="pixlikes_settings[show_on_home]" value="'. (($options['show_on_home']) ? '1' : '0') .'"'. (($options['show_on_home']) ? ' checked="checked"' : '') .'/>'.
			'<label for="pixlikes_settings[show_on_home]">Home page</label>'.
			'</fieldset>';

		echo '<fieldset>'.
			'<input type="checkbox" name="pixlikes_settings[show_on_archives]" value="'. (($options['show_on_archives']) ? '1' : '0') .'"'. (($options['show_on_archives']) ? ' checked="checked"' : '') .'/>'.
			'<label for="pixlikes_settings[show_on_archives]">Archives like blog, categories, search page</label>'.
			'</fieldset>';
	}

	public function setting_load_likes_with_ajax() {
		$options = get_option( 'pixlikes_settings' );
		if( !isset($options['load_likes_with_ajax']) ) $options['load_likes_with_ajax'] = '0';

		echo '<div>';
		echo '<fieldset>'.
			'<input type="checkbox" name="pixlikes_settings[load_likes_with_ajax]" value="'. (($options['load_likes_with_ajax']) ? '1' : '0') .'"'. (($options['load_likes_with_ajax']) ? ' checked="checked"' : '') .'/>'.
			'<label for="pixlikes_settings[load_likes_with_ajax]">This helps you to prevent the likes number to be cached </label>'.
			'</fieldset></div>';
	}

	/**
	 * Loading the likes box template.
	 * The template loaded is found in views/pixlikes-template.php but it can be overridden in theme/child-theme by creating a file in templates/pixlikes-template.php
	 *
	 * @param    array    $_vars    Array with arguments like "display_only" which if is set true users can not vote on that box.
	 * @return   string   Return the template
	 */
	public function loadTemplate( $_vars = array( 'display_only' => '' ) ){

		$_name = 'pixlikes-template';
		$_located = locate_template("templates/{$_name}.php", false, false);

		// use the default one if the (child) theme doesn't have it
		if(!$_located) {
			$_located = dirname(__FILE__).'/views/'.$_name.'.php';
		}
		unset($_name);
		$class = '';
		// create variables
		if( !empty($_vars)) {
			extract($_vars);
		}

		if ( empty($display_only) ) {
			$display_only = 'can_like';
		} else {
			$display_only = '';
		}
		$data_id = 'data-id="'.get_the_ID().'"';
		$likes_number = $this->get_likes_number(get_the_ID());

		if ( empty($likes_number) ) {
			$likes_number = 0;
		}

		$title = '';
		if( isset( $_COOKIE['pixlikes_'. get_the_ID()]) && $display_only == 'can_like' ) {
			$title = __('You already voted!', wpGrade_txtd);
		}

		// load it
		ob_start();
		require $_located;
		return ob_get_clean();
	}

	/**
	 * Add the like box after the content if the user choose it
	 *
	 * @param    string     $content    Content of the post
	 * @return   string     $content    The content and the like box if is needed
	 */
	public function add_like_box_after_content( $content ){
		$options = $this->get_settings();
		// homepages
		if ( ( is_front_page() || is_home() ) && $options['show_on_home'] == '1' ) return $content . $this->loadTemplate(array( 'display_only' => true ));
		// archives
		if ( ( is_archive() || is_search() ) && $options['show_on_archives'] == '1') return $content . $this->loadTemplate(array( 'display_only' => true ));
		// singulars
		if( is_singular('post') && $options['show_on_post'] == '1' ) return $content . $this->loadTemplate();
		if( is_page() && !is_front_page() && $options['show_on_page'] == '1' ) return $content . $this->loadTemplate();
		// custom post types
		$post_type = get_post_type();
		if ( 'post' !== $post_type || 'page' !== $post_type ) {

			if( is_singular($post_type) && $options['show_on_'.$post_type] == '1' ) return $content . $this->loadTemplate();
			// check also for a custom post type archive
			if ( is_post_type_archive($post_type) && $options['show_on_'.$post_type] == '1' ) return $content . $this->loadTemplate(array( 'display_only' => true ));

		}

		return $content;
	}

	/**
	 * If the action requested is to like, update the data base otherwise just return the likes number
	 *
	 * @param    int    $post_id        Id of the post
	 * @return   int    $likes_number   Number of likes
	 */
	public function ajax_callback(){

		$result = array('success' => false, 'msg' => 'Nothing happend', 'likes_number' => 0 );
//		if ( !check_ajax_referer('pixlikes')) {
//			$result['msg'] = "No naughty business please";
//			echo json_encode($result);
//			exit();
//		}

		$post_id = $_REQUEST['post_id'];
		$likes_number = $this->get_likes_number($post_id);

		if ( $_REQUEST['type'] == "get" ) {
			$result['likes_number'] = $likes_number;
//			$result['msg'] = '';
			$result['success'] = true;
		} elseif ( $_REQUEST['type'] == "increment" ) {

			// if the user already voted return the curent likes number
			if( isset($_COOKIE['pixlikes_'. $post_id]) ) {
				$result['likes_number'] = $likes_number;
				$result['msg'] = 'You already voted!';
				echo json_encode($result);
				exit();
			}
			$likes_number++;
			$result['likes_number'] = $likes_number;
			update_post_meta($post_id, '_pixlikes', $likes_number );
			setcookie('pixlikes_'. $post_id, true, time()*20, '/');
			$result['success'] = true;
			$result['msg'] = "Thank you! We like you too";
		}
		echo json_encode($result);
		exit;
	}

	/*
	 * Get all settings in one array but with values initialized
	 *
	 * @return   array      $content   All options, ones without values get initialized with string "0"
	 */
	public function get_settings(){

		$options = get_option( 'pixlikes_settings' );
		if( !isset($options['show_on_post']) ) $options['show_on_post'] = '0';
		if( !isset($options['show_on_page']) ) $options['show_on_page'] = '0';
		if( !isset($options['show_on_home']) ) $options['show_on_home'] = '0';
		if( !isset($options['show_on_archives']) ) $options['show_on_archives'] = '0';
		if( !isset($options['load_likes_with_ajax']) ) $options['load_likes_with_ajax'] = '0';

		$post_types = get_post_types( array(
			'public'   => true,
			'_builtin' => false
		), 'names' );

		foreach ( $post_types as $post_type ) {
			$add_to_string = 'show_on_'.$post_type;
			if( !isset($options[$add_to_string]) ) $options[$add_to_string] = '0';
		}

		return $options;
	}

	/*
	 * Display the like box unconditionally
	 */
//	public function display_pixlikes( $args = array('display_only' => false, 'class' => '' ) ) {
//		echo $this->loadTemplate($args);
//	}

	public function display_likes_number( $args ) {
		echo $this->loadTemplate( $args );
	}

	/*
	 * Return likes number
	 * @param   $post_id
	 */

	public function get_likes_number($post_id){

		$curent_likes = get_post_meta( $post_id, '_pixlikes', true );
		if ( !empty($curent_likes) ) {
			return $curent_likes;
		} else {
			return 0;
		}
	}
}
