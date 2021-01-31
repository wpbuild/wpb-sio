<?php
	if( ! defined( 'ABSPATH' ) ) exit; // Закрыть прямой доступ

	class WPB_Sio_Front
{
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
	var $optimizer;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $settings=array() ) {
		
		$enable_caching = false;
		$caching_ttl = 3600;
		$settings = get_option('wpb_options');

		$this->settings = $settings;
   		$this->settings = apply_filters('wpb_sio_optimizer_settings', $this->settings);// Фильтр настроек

		$this->enable_caching = $this->settings['enable_caching'];
		$this->caching_ttl = $caching_ttl;
		$this->plugin = $plugin;
	}

	/**
	 * initialize frontend functions
	 *
	 * @return void
	 */
	public function initialize()
	{

		// process post thumbnails
		if( isset($this->settings['thumbnail_override']) && ($this->settings['thumbnail_override'] !== 'off' ) )
		{

		    if( function_exists('is_woocommerce') && is_woocommerce() ) 
		    {
                add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_attributes' ], 10, 2 );
            } 
            else 
            {
                add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_attributes' ], 10, 2 );
                add_filter( 'td_wpbsio_attachment_info', [ $this, 'optimize_image_attributes' ], 10, 2 );
            }

		} 
		else if( function_exists('is_woocommerce') && is_woocommerce() ) 
		{
            add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_attributes' ], 10, 2 );
        }

		// process post images
		if( !is_front_page()){
		if( isset($this->settings['title_override']) &&isset($this->settings['alt_override']) ) 
		{
			if ( ($this->settings['title_override'] == 'yes' || $this->settings['title_override'] == 'no') && ($this->settings['alt_override'] == 'yes' || $this->settings['alt_override'] == 'no') )
			{
	    	    //print_r($this->settings);
				add_filter( 'the_content', [ $this, 'optimize_html' ], 999, 1 );
				add_filter('acf/load_value/type=wysiwyg', [ $this, 'optimize_html' ], 20, 3);// Support for AdvancedCustomFields
			}
	    }
	}
	}

	/**
	 * Check if the optimizer is already initialized and initialize if not
	 *
	 * @return void
	 */
	private function _maybe_initialize_optimizer()
	{
		if( false === is_a($this->optimizer, 'WPB_Sio_Optimizer') ) {
			$this->optimizer = new WPB_Sio_Optimizer( $this->settings );
		}
	}

    /**
     * Optimize given HTML code
     *
     * @param string $content
     *
     * @param int $post_id
     * @param null|string $field
     * @return string
     */
	public function optimize_html( $content, $post_id=0, $field=null )
	{
	    if( $post_id === 0 ) {
	        $post_id = get_the_ID();
        }

	    $caching = apply_filters('wpbsio_optimize_html_caching', $this->enable_caching, $post_id, $field);

	    
	    // Get Cache
	    if( $caching ) {
	        $cache = new WPB_Sio_Cache($this->enable_caching, $this->caching_ttl);

	        $cache_key =  'post_'.$post_id.(!empty($field) ? '_'.$field : '');
	    
            $cache_item = $cache->get_cache( $cache_key );

            if( $cache_item )
                return $cache_item;
        }

		// maybe initialize the optimizer class
		$this->_maybe_initialize_optimizer();

		// optimize html
		$content = $this->optimizer->optimize_html( $content );

		// Set Cache
        if( $caching && isset($cache_key) && isset($cache) ) {

            $cache->set_cache($cache_key, $content);
        }

		return $content;
	}

	/**
	 * Add image title and alt to post thumbnails
	 *
	 * @param array $attr
	 * @param WP_Post $attachment
	 * @return array
	 */
	public function optimize_image_attributes( $attr, $attachment = null )
	{
		$this->_maybe_initialize_optimizer();
		$attr = $this->optimizer->optimize_image_attributes( $attr, $attachment );
		return $attr;
	}
}