<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://vtqnm.xyz
 * @since      1.0.0
 *
 * @package    Vtqnm_Simple_View_Counter
 * @subpackage Vtqnm_Simple_View_Counter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Vtqnm_Simple_View_Counter
 * @subpackage Vtqnm_Simple_View_Counter/public
 * @author     Vitalii Terentev <vtqnm0@gmail.com>
 */
class Vtqnm_Simple_View_Counter_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $settings ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->settings = $settings;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vtqnm_Simple_View_Counter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vtqnm_Simple_View_Counter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vtqnm-simple-view-counter-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vtqnm_Simple_View_Counter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vtqnm_Simple_View_Counter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        if (is_singular((!empty($this->settings['post_type']) ? $this->settings['post_type'] : null))) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/vtqnm-simple-view-counter-public.js', array('jquery'), $this->version, false);
            wp_add_inline_script($this->plugin_name, $this->get_post_view_counter_inline_script());
        }
	}

    private function get_post_view_counter_inline_script()
    {
        $delay = $this->settings['delay'] ?: '';

        $url = admin_url('admin-ajax.php');
        $nonce = wp_create_nonce( $this->plugin_name );

        $id = get_the_ID();
        return "
            const instance = new Vtqnm.PostViews({url: '$url', nonce: '$nonce'});
            instance.markViewedAfterDelay($id, $delay);
        ";
    }
}
