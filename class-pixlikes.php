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
class PixLikesPlugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @const   string
	 */

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
	 * Keep plugin options here
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	protected static $options = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */

	protected $config;

	private function __construct() {

		// get options
		self::$options = get_option('pixlikes_settings');

		$this->config = self::config();

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'admin_init', array( $this, 'wpgrade_init_plugin' ) );
		// Add the options page and menu item.
		 add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		 $plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'pixlikes.php' );
		 add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		/// think about including this in add_like_box_after_content() so the script fille to be included only when is needed
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// when the user is publishing a post let's ensure he is creating a default likes meta
		add_action( 'publish_post', array( $this, 'init_meta_likes' ) );

		// prepend the like box after the content
		add_filter('the_content', array(&$this, 'add_like_box_after_content'));

		add_action('wp_ajax_pixlikes', array(&$this, 'ajax_callback'));
		add_action('wp_ajax_nopriv_pixlikes', array(&$this, 'ajax_callback'));

		// prepend the display_likes after the excerpt
		//add_filter('the_excerpt', array(&$this, 'the_content'));

		// edit metabox
		if ( isset(self::$options['edit_votes']) && self::$options['edit_votes'] ) {
			add_action( 'save_post', array(&$this,'pixlikes_save_postdata') );
			add_action( 'add_meta_boxes', array(&$this,'adding_pixlikes_custom_meta_boxes'), 10, 2 );
		}
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

	public static function config(){
		// @TODO maybe check this
		return include 'plugin-config.php';
	}

	public function wpgrade_init_plugin(){
//		$this->plugin_textdomain();
//		$this->add_wpgrade_shortcodes_button();
		$this->github_plugin_updater_init();
	}

	/**
	 * Ensure github updates
	 * Define an update branch and config it here
	 */
	public function github_plugin_updater_init() {
		include_once 'updater.php';
//		define( 'WP_GITHUB_FORCE_UPDATE', true ); // this is only for testing
		if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
			$git_config = $this->config['github_updater'];
			$this->github_updater = new WP_Pixlikes_GitHub_Updater( $git_config );
		}
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

		$options = self::$options;

		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );

		$time = 1000;
		if ( $options['like_action'] == 'hover' ) {
			$time = $options['hover_time'];
		}

//		$custom_css = '.complete i {'.
//			'animation: bounce '.$time.';'.
//			'-webkit-animation: bounce '.$time.'; }';

		$custom_css = '.animate i:after {'.
			'-webkit-transition: all '.$time.'ms;'.
			'-moz-transition: all '.$time.'ms;'.
			'-o-transition: all '.$time.'ms;'.
			'transition: all '.$time.'ms; }';
		wp_add_inline_style($this->plugin_slug . '-plugin-styles', $custom_css );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$options = self::$options;
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		$nonce = wp_create_nonce( 'pixlikes' );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'locals',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nounce' => $nonce,
				'load_likes_with_ajax' => $options['load_likes_with_ajax'],
				'already_voted_msg' => __("You already voted!", pixlikes::textdomain()),
				'like_on_action' => $options['like_action'],
				'hover_time' => $options['hover_time'],
				'free_votes' => $options['free_votes']
			)
		);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'PixLikes', pixlikes::textdomain() ),
			__( 'PixLikes', pixlikes::textdomain() ),
			'manage_options',
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
				'settings' => '<a href="' . admin_url( 'options-general.php?page=pixlikes' ) . '">' . __( 'Settings', pixlikes::textdomain() ) . '</a>'
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

		if ( empty( $display_only ) && !$this->has_post_cookie( get_the_ID() ) ) {
			$display_only = 'likeable';
		} else {
			$display_only = 'liked';
		}
		$data_id = 'data-id="'.get_the_ID().'"';
		$likes_number = $this->get_likes_number(get_the_ID());

		if ( empty($likes_number) ) {
			$likes_number = 0;
		}

		$title = '';
		if( $this->has_post_cookie( get_the_ID() ) && $display_only == 'likeable' ) {
			$title = __('You already voted!', pixlikes::textdomain());
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
		$options = self::$options;
		// homepages
		if ( ( is_front_page() || is_home() ) && $options['show_on_hompage'] == '1' ) return $content . $this->loadTemplate(array( 'display_only' => true ));
		// archives
		if ( ( is_archive() || is_search() ) && $options['show_on_archive'] == '1') return $content . $this->loadTemplate(array( 'display_only' => true ));
		// singulars
		if( is_singular('post') && $options['show_on_post'] == '1' ) return $content . $this->loadTemplate();
		if( is_page() && !is_front_page() && $options['show_on_page'] == '1' ) return $content . $this->loadTemplate();
		// custom post types
		$post_type = get_post_type();
		if ( 'post' !== $post_type || 'page' !== $post_type ) {

			if( is_singular($post_type) && isset($options['show_on_'.$post_type]) && $options['show_on_'.$post_type] == '1' ) return $content . $this->loadTemplate();
			// check also for a custom post type archive
			if ( is_post_type_archive($post_type) && isset($options['show_on_'.$post_type]) && $options['show_on_'.$post_type] == '1' ) return $content . $this->loadTemplate(array( 'display_only' => true ));

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
			if( $this->has_post_cookie( $post_id ) ) {
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

	function adding_pixlikes_custom_meta_boxes( $post_type, $post ) {
		add_meta_box(
			'edit_pixlikes',
			__( 'Pixlikes', pixlikes::textdomain() ),
			array(&$this,'pixlikes_edit_metabox'),
			'',
			'side',
			'low'
		);
	}

	function pixlikes_edit_metabox( $post ){

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'pixlikes_inner_custom_box', 'pixlikes_inner_custom_box_nonce' );
		$value = get_post_meta( $post->ID, '_pixlikes', true );

		echo '<label for="edit_pixlikes">';
		_e( "Likes number: ", pixlikes::textdomain() );
		echo '</label> ';
		echo '<input type="text" id="edit_pixlikes" name="edit_pixlikes" value="' . esc_attr( $value ) . '" size="20" />';

	}

	function pixlikes_save_postdata( $post_id ) {

		/*
		   * We need to verify this came from the our screen and with proper authorization,
		   * because save_post can be triggered at other times.
		   */

		// Check if our nonce is set.
		if ( ! isset( $_POST['pixlikes_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['pixlikes_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'pixlikes_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize user input.
		$pixlikes = sanitize_text_field( $_POST['edit_pixlikes'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_pixlikes', $pixlikes );
	}



	/*
	 * Display the like box
	 */

	function display_likes_number( $args ) {
		echo $this->loadTemplate( $args );
	}

	/*
	 * Return likes number
	 * @param   $post_id
	 * @return int likes number
	 */

	public function get_likes_number($post_id){
		$curent_likes = get_post_meta( $post_id, '_pixlikes', true );
		if ( !empty($curent_likes) ) {
			return intval($curent_likes);
		} else {
			return intval(0);
		}
	}

	/**
	 * Check cookie for a post
	 */
	function has_post_cookie( $post_id ){
		$options = self::$options;
		if ( $options['free_votes'] ) {
			return false;
		} elseif ( isset( $_COOKIE['pixlikes_'.$post_id] ) ) {
			return true;
		}
		return false;
	}
}
