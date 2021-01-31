<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wpbuild.ru
 * @since      1.0.0
 *
 * @package    Wpb_Sio
 * @subpackage Wpb_Sio/includes
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
 * @package    Wpb_Sio
 * @subpackage Wpb_Sio/includes
 * @author     wpbuild <dev@wpbuild.ru>
 */
class Wpb_Sio {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wpb_Sio_Loader    $loader    Maintains and registers all hooks for the plugin.
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

	/**
	 * Plugin Data
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $plugin    Plugin Data.
	 */
	protected $plugin = array();

	/**
	 * Plugin Settings
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $settings    Plugin Settings.
	 */
	protected $settings = array();

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
		if ( defined( 'WPB_SIO_VERSION' ) ) {
			$this->version = WPB_SIO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wpb-sio';
		$this->option_name = 'wpb_options';
//		$this->settings = get_option($this->option_name);

		// Plugin Data
		$this->plugin = array(
			'name'				=> __('WPB SEO Images Optimizer', 'wpb-sio'),
			'plugin_name'		=> $this->plugin_name,
			'version'			=> $this->version,
			'path'				=> plugin_dir_path( dirname( __FILE__ ) ),
			'url'				=> plugin_dir_url( dirname( __FILE__ ) ),
		);

		$this->load_dependencies();
		$this->set_locale();
		$this->load_options();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->initialize_frontend();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wpb_Sio_Loader. Orchestrates the hooks of the plugin.
	 * - Wpb_Sio_i18n. Defines internationalization functionality.
	 * - Wpb_Sio_Admin. Defines all hooks for the admin area.
	 * - Wpb_Sio_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpb-sio-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpb-sio-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpb-sio-admin.php';
		require_once $this->plugin['path'] . 'admin/class-wpb-settings-api.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpb-sio-public.php';
		require_once $this->plugin['path'] . 'public/inc/class-wpb-frontend.php';
		require_once $this->plugin['path'] . 'public/inc/class-wpb-optimizer.php';
		require_once $this->plugin['path'] . 'public/inc/class-wpb-cache.php';

		$this->loader = new Wpb_Sio_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wpb_Sio_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wpb_Sio_i18n();

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

		$plugin_admin = new Wpb_Sio_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_data() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// Add menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );

		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

	   // Save/Update our plugin options
	   $this->loader->add_action('admin_init', $plugin_admin, 'init_settings');

		// Add Clear Cache
        if( isset($this->settings['enable_caching']) && $this->settings['enable_caching'] == true ) {
           add_action('admin_bar_menu', [ $this, 'admin_bar_menu' ], 999);
           add_action('post_updated', [ $this, 'clear_cache_for_post' ], 10, 3);
        }
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wpb_Sio_Public( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_data() );
		$front = new Wpb_Sio_Front( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_data() );
		$optimizer = new Wpb_Sio_Optimizer( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_data() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        $this->loader->add_filter( 'wp_calculate_image_srcset', $plugin_public, '__return_false' );
        $this->loader->add_filter( 'wp_calculate_image_srcset_meta', $plugin_public, '__return_null' );


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
	 * @return    Wpb_Sio_Loader    Orchestrates the hooks of the plugin.
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


	/**
	 * Retrieve the plugin data.
	 *
	 * @since     1.0.0
	 * @return    array    Plugin Data.
	 */
	public function get_plugin_data() {
		return $this->plugin;
	}


	/**
	 * Retrieve the plugin settings.
	 *
	 * @since     1.0.0
	 * @return    array    Get Plugin Settings.
	 */
	public function get_settings() {
		return $this->settings;
	}

		/**
		 * Load Plugin Settings from Options
		 * @return void
		 */
		public function load_options()
		{
			$settings_from_option = get_option($this->option_name);
			if( is_array($settings_from_option) ) {

				$this->settings = array_merge(
					$this->settings,
					$settings_from_option
				);

			}

		}


		/**
		 * initialize frontend functions
		 * @return void
		 */
		public function initialize_frontend()
		{
			$frontend = new WPB_Sio_Front( $this );
			add_action('template_redirect', array($frontend, 'initialize'));
		}

      
        
        /**
         * AdminBar Menu for Cache
         */
        public function admin_bar_menu()
        {
            global $wp_admin_bar;

            $menu_id = 'wpbsio';

            $wp_admin_bar->add_menu(array(
                'id' => $menu_id,
                'title' => __('Clear Image Cache', 'wpb-sio'),
                'href' => admin_url('options-general.php?page=wpb-sio&clear_cache=true')
            ));
        }


        public function clear_cache_for_post($post_ID, $post_after, $post_before)
        {
            $cache = new WPB_Sio_Cache();
            $cache->clear_post_cache( $post_ID );
        }



}

