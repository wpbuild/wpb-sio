<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wpbuild.ru
 * @since      1.0.0
 *
 * @package    Wpb_Sio
 * @subpackage Wpb_Sio/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpb_Sio
 * @subpackage Wpb_Sio/admin
 * @author     wpbuild <dev@wpbuild.ru>
 */
class Wpb_Sio_Admin {

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

	/**
	 * Plugin Data
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $plugin    Plugin Data.
	 */
	protected $plugin;

	/**
	 * Plugin Settings
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $settings    Plugin Settings.
	 */
	private $settings;
	
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin = $plugin;
		$this->settings =  new WeDevs_Settings_API;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpb_Sio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpb_Sio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpb-sio-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpb_Sio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpb_Sio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpb-sio-admin.js', array('jquery','jquery-ui-core','jquery-ui-tabs'), $this->version, false );

	}


    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     */

    public function add_plugin_admin_menu() {

     /*
      * Add a settings page for this plugin to the Settings menu.
     */
        add_options_page( 'WPBSIO', 'SIO', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
        );
    }

     /**
     * Add settings action link to the plugins page.
     */

    public function add_action_links( $links ) {
        
       $settings_link = array(
        '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
       );
       return array_merge(  $settings_link, $links );

    }

    /**
     * Render the settings page for this plugin.
     */

    public function display_plugin_setup_page() {

		if ( ! current_user_can('manage_options') ) {
			return;
		}
		// Backend Template
        // include_once( 'partials/wpb-sio-admin-display.php' );

    	if( isset($_GET['clear_cache']) && $_GET['clear_cache'] === 'true' ) {
        $cache = new WPB_Sio_Cache();
        $clear_cache = $cache->clear_cache();
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo sprintf(__( '<strong>Cache cleared!</strong> %d elements are removed from transient cache.', 'pb-seo-friendly-images' ), $clear_cache); ?></p>
        </div>
<?php
	    }
   		echo '<div class="'.$this->plugin_name.'">';
		echo '<div class="wrap">';
	        $this->settings->show_navigation();
    	    $this->settings->show_forms();
		echo '</div>';
		echo '</div>';
    } 

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

	/**
	* Get the value of a settings field
	*
	* @param string $option settings field name
	* @param string $section the section name this field belongs to
	* @param string $default default text if it's not found
	* @return mixed
	*/
	function wpb_get_option( $option, $section, $default = '' ) {

	    $options = get_option( $section );
 
    	if ( isset( $options[$option] ) ) {
	    return $options[$option];
    	}
 
	    return $default;
	}
	
	public function	init_settings() {
		//set the settings
        $this->settings->set_sections( $this->get_settings_sections() );
        $this->settings->set_fields( $this->get_settings_fields() );

		//initialize settings
        $this->settings->admin_init();
	} 

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'wpb_options',
                'title' => __( 'Settings', 'wedevs' )
            ),
            array(
                'id'    => 'wpb_help',
                'title' => __( 'Help', 'wedevs' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'wpb_options' => array(
                array(
                    'name'    => 'help_0',
                    'label'   => __( 'Доступные переменные', 'wedevs' ),
                    'desc'    => __( '%name, %category, %title, %tags', 'wedevs' ),
                    'type'    => 'html',
                ),
                array(
                    'name'    => 'alt_override',
                    'label'   => __( 'Перезаписать значение атрибута Alt', 'wedevs' ),
                    'desc'    => __( 'Yes - Перезаписать все<br>No - Заменить пустые значения<br>Off - Оставить как есть', 'wedevs' ),
                    'type'    => 'select',
                    'default' => 'off',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No',
                        'off' => 'Off'
                    )
                ),
                array(
                    'name'              => 'alt_val',
                    'label'             => __( 'Значение атрибута Alt', 'wedevs' ),
                    'desc'              => __( '', 'wedevs' ),
                    'placeholder'       => __( '', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '%name %title',
                    'sanitize_callback' => 'esc_html'
                ),
                array(
                    'name'    => 'title_override',
                    'label'   => __( 'Перезаписать значение атрибута Title', 'wedevs' ),
                    'desc'    => __( '', 'wedevs' ),
                    'type'    => 'select',
                    'default' => 'off',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No',
                        'off' => 'Off'
                    )
                ),
                array(
                    'name'    => 'title_cap',
                    'label'   => __( 'Перезаписать значение атрибута Title значением из Caption', 'wedevs' ),
                    'desc'    => __( '', 'wedevs' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No',
                    )
                ),
                array(
                    'name'              => 'title_val',
                    'label'             => __( 'Значение атрибута Title', 'wedevs' ),
                    'desc'              => __( '', 'wedevs' ),
                    'placeholder'       => __( '', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '%title',
                    'sanitize_callback' => 'esc_html'
                ),
                array(
                    'name'    => 'hr_0',
                    'desc'    => __( '<hr>', 'wedevs' ),
                    'type'    => 'html',
                ),
                array(
                    'name'    => 'thumbnail_override',
                    'label'   => __( 'Перезаписать значение атрибутов для миниатюр', 'wedevs' ),
                    'desc'    => __( '', 'wedevs' ),
                    'type'    => 'select',
                    'default' => 'off',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No',
                        'off' => 'Off'
                    )
                ),
                array(
                    'name'              => 'thumbnail_alt_val',
                    'label'             => __( 'Значение атрибута Alt для миниатюры', 'wedevs' ),
                    'desc'              => __( '', 'wedevs' ),
                    'placeholder'       => __( '', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '%title',
                    'sanitize_callback' => 'esc_html'
                ),
                array(
                    'name'              => 'thumbnail_title_val',
                    'label'             => __( 'Значение атрибута Title для миниатюры', 'wedevs' ),
                    'desc'              => __( '', 'wedevs' ),
                    'placeholder'       => __( '', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '%title',
                    'sanitize_callback' => 'esc_html'
                ),
                array(
                    'name'    => 'hr_1',
                    'desc'    => __( '<hr>', 'wedevs' ),
                    'type'    => 'html',
                ),
                array(
                    'name'              => 'replace_val',
                    'label'             => __( 'Значения для подстановки', 'wedevs' ),
                    'desc'              => __( 'Значения для подстановки, через запятую без пробелов.', 'wedevs' ),
                    'placeholder'       => __( '', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'    => 'hr_2',
                    'desc'    => __( '<hr>', 'wedevs' ),
                    'type'    => 'html',
                ),
                array(
                    'name'    => 'wc_override',
                    'label'   => __( 'Перезаписать значение атрибутов Title и Alt для WC', 'wedevs' ),
                    'desc'    => __( '', 'wedevs' ),
                    'type'    => 'select',
                    'default' => 'off',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No',
                        'off' => 'Off'
                    )
                ),
                array(
                    'name'    => 'yost_primary_cat_override',
                    'label'   => __( 'Включить поддержку Yoast primary category?', 'wedevs' ),
                    'desc'    => __( '', 'wedevs' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'enable_caching',
                    'label'   => __( 'Включить кэширование результатов замены?', 'wedevs' ),
                    'desc'    => __( '', 'wedevs' ),
                    'type'    => 'select',
                    'default' => false,
                    'options' => array(
                        true  => 'Yes',
                        false => 'No'
                    )
                ),
             ),
            'wpb_help' => array(
                array(
                    'name'    => 'vote_1',
                    'desc'    => __( '<h3>Переменные</h3>', 'wedevs' ),
                    'type'    => 'html',
                ),

                array(
                    'name'    => 'help_1',
                    'desc'    => __( '%name - Имя файла изображения<br>%title - Заголовок поста<br>%category - Категории поста, если включена поддержка Yoast, показывается либо основная, либо первая<br>%tags - Теги поста', 'wedevs' ),
                    'type'    => 'html',
                ),
            )
        );

        return $settings_fields;
    }


}

