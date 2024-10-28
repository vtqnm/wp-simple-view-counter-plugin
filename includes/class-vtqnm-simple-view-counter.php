<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://vtqnm.xyz
 * @since      1.0.0
 *
 * @package    Vtqnm_Simple_View_Counter
 * @subpackage Vtqnm_Simple_View_Counter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Vtqnm_Simple_View_Counter
 * @subpackage Vtqnm_Simple_View_Counter/includes
 * @author     Vitalii Terentev <vtqnm0@gmail.com>
 */
class Vtqnm_Simple_View_Counter {
    const PLUGIN_NAME = 'vtqnm-simple-view-counter';

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Vtqnm_Simple_View_Counter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

    /** @var array{post_type: string[]} */
    protected $default_settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'VTQNM_SIMPLE_VIEW_COUNTER_VERSION' ) ) {
			$this->version = VTQNM_SIMPLE_VIEW_COUNTER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = self::PLUGIN_NAME;
        $this->default_settings = [
            'delay' => 5
        ];

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
        $this->define_ajax_hooks();
	}

    public static function can_update_post_views(WP_Post $post) {
        if (
            $post->post_status !== 'publish' ||
            !in_array($post->post_type, ['post', 'page'])
        ) {
            return false;
        }

        return true;
    }

    public static function get_post_views($id = null) {
        if (!is_numeric($id)) {
            $id = get_the_ID();
        }

        $post = get_post($id);
        if (!self::can_update_post_views($post)) {
            return false;
        }

        $count = get_post_meta($id, self::PLUGIN_NAME, true);
        return (filter_var($count, FILTER_VALIDATE_INT) && $count > 0) ? $count : 0;
    }

    public function update_view_counter($post_id) {
        $post = get_post($post_id);
        if (!$post || !$this->can_update_post_views($post)) {
            return false;
        }

        $count = self::get_post_views($post->ID);
        update_post_meta($post->ID, $this->plugin_name, ++$count);
        return true;
    }

    public function ajax_update_views_counter() {
        $post_id = filter_var($_POST["post_id"], FILTER_SANITIZE_NUMBER_INT);
        if ($post_id < 1 || !wp_verify_nonce($_POST["nonce"], $this->plugin_name) || !setup_postdata($post_id) || !$this->update_view_counter($post_id)) {
            wp_send_json_error('error');
        }

        wp_die();
    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Vtqnm_Simple_View_Counter_Loader. Orchestrates the hooks of the plugin.
	 * - Vtqnm_Simple_View_Counter_i18n. Defines internationalization functionality.
	 * - Vtqnm_Simple_View_Counter_Admin. Defines all hooks for the admin area.
	 * - Vtqnm_Simple_View_Counter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vtqnm-simple-view-counter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vtqnm-simple-view-counter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vtqnm-simple-view-counter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vtqnm-simple-view-counter-public.php';

		$this->loader = new Vtqnm_Simple_View_Counter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Vtqnm_Simple_View_Counter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Vtqnm_Simple_View_Counter_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Vtqnm_Simple_View_Counter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Vtqnm_Simple_View_Counter_Public( $this->get_plugin_name(), $this->get_version(), $this->default_settings );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

    private function define_ajax_hooks() {
        $this->loader->add_action( 'wp_ajax_update_views_counter', $this, 'ajax_update_views_counter' );
        $this->loader->add_action( 'wp_ajax_nopriv_update_views_counter', $this, 'ajax_update_views_counter' );
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Vtqnm_Simple_View_Counter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
