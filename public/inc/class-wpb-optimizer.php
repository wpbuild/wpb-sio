<?php
	if( ! defined( 'ABSPATH' ) ) exit; // Закрыть прямой доступ

	class WPB_Sio_Optimizer
{
	protected $settings = array(); // Настройки
	protected $defaults = array(); // Значения по умолчанию
	protected $document; // @var DOM

	public function __construct( $settings=array() ) {

		$charset = ( (defined('DB_CHARSET') ) ? DB_CHARSET : 'utf-8' );	

		// Дефолтные значения
		$this->defaults = array(
			'encoding_charset' => $charset,
			'encoding_mode' => 'off',
			'create_domdocument' => true
		);
		$settings = get_option('wpb_options');

		// Замена дефолтных настроек
		$this->settings = array_merge(
			$this->defaults,
			$settings
		);
		
		// Фильтр настроек
		$this->settings = apply_filters('wpb_sio_optimizer_settings', $this->settings);


		if( class_exists('DOMDocument') && false !== $this->settings['create_domdocument'] ) {

			$this->document = new DOMDocument( '1.0', $this->settings['encoding_charset'] );

			$this->document = apply_filters(
				'wpb_sio_optimizer_domdocument', // фильтр
				$this->document, // Новый DOMDocument
				$this->settings // Настройки
			);
		}
	}

	/**
	 * Get array key
	 *
	 * @param $key
	 * @param $array
	 * @return bool
	 */

	public function get_array_key($key, $array)
	{
		if( array_key_exists($key, $array) ) {
			return $array[$key];
		} else {
			return false;
		}
	}
	
	/**
	 * Check if content could be optimized
	 *
	 * @param array $data data for the optimization check
	 * @return bool
	 */
	public function is_optimization_allowed( $data=array() )
	{
		if(
			! class_exists('DOMDocument') || // do not run optimization, DOMDocument not available
			get_post_type() == 'tribe_events' || // exclude for Events Calendar
			is_feed() || // exclude for feeds
			strstr( strtolower($data['content']), 'arforms') || // exclude for ARForms
			strstr( strtolower($data['content']), 'arf_form') || // exclude for ARForms
			strstr( strtolower($data['content']), 'spb_gallery') || // exclude for spb_gallery_widget
			isset($_GET['dslc']) // exclude for LiveComposer
		) {
			return apply_filters( 'is_optimization_allowed', false, $data );
		}

		return apply_filters( 'is_optimization_allowed', true, $data );
	}
	
	/**
	 * Get image id by url
	 *
	 * @param $url
	 * @return bool|int
	 */
	public function get_image_id($url)
	{
		global $wpdb;

		$transient_name = sanitize_title( $url );
		$transient = get_transient( $transient_name );
		 
		// return numeric transient
		if( false !== $transient && is_numeric($transient) ) {
			return (int) $transient;
		}

		// transient with false string (details below)
		if( false !== $transient && 'false' === $transient ) {
			return false;
		}

		$sql = $wpdb->prepare(
			'SELECT `ID` FROM `'.$wpdb->posts.'` WHERE `guid` = \'%s\';',
			esc_sql($url)
		);

		$attachment = $wpdb->get_col($sql);

		if( is_numeric( $this->get_array_key(0, $attachment) ) ) {

			// set transient
			set_transient( $transient_name, $attachment[0], YEAR_IN_SECONDS );

			return (int) $attachment[0];
		}

		// set transient => false as string because transients can't deal with boolean values
		set_transient( $transient_name, 'false', YEAR_IN_SECONDS );

		return false;
	}
	
	public function convert_replacements( $content, $data=array() )
	{

		$post = get_post();

		$imageID = ( (isset($data['image_id'])) ? $data['image_id'] : '' );
		$src = ( (isset($data['src'])) ? $data['src'] : '');
		//$j = ( (isset($data['j'])) ? $data['j'] : false );
		//$replace_val = ( (isset($this->settings['replace_val'])) ? explode(",", $this->settings['replace_val'] ) : false );
		$replace_val = ( (isset($data['replace_val'])) ? $data['replace_val'] : '' );
		$yoast = ( (isset($data['yoast'])) ? $data['yoast'] : 'no' );

       // Get post categories
		$cats = '';
        if ($yoast=='yes'){
			if ( strrpos( $content, '%category' ) !== false ) {
			  if ( class_exists('WPSEO_Primary_Term') ) {
				$wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
				$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
    			$term = get_term( $wpseo_primary_term );
		    		if (is_wp_error($term)) {
						$categories = get_the_terms(get_the_ID(), 'category');
						$cats = $categories[0]->name;
					} else {
						$cats = $term->name;
					}

				} else {
					$categories = get_the_terms(get_the_ID(), 'category');
					$cats = $categories[0]->name;
				}
			}
        
        } else {
            if ( strrpos( $content, '%category' ) !== false ) {
 	        	$categories = get_the_category();
				if ( $categories ) {
					$i = 0;
					foreach ( $categories as $cat ) {
						if ( $i == 0 ) {
							$cats = $cat->name . $cats;
						} else {
							$cats = $cat->name . ', ' . $cats;
						}
						++$i;
					}
				}
			}
        }

		$tags = '';
		if ( strrpos( $content, '%tags' ) !== false ) {
			$posttags = get_the_tags();

			if ( $posttags ) {
				$i = 0;
				foreach ( $posttags as $tag ) {
					if ( $i == 0 ) {
						$tags = $tag->name . $tags;
					} else {
						$tags = $tag->name . ', ' . $tags;
					}
					++$i;
				}
			}
		}

		if( $src ) {
			$info = @pathinfo($src);
			$src = @basename($src,'.'.$info['extension']);

			$src = str_replace('-', ' ', $src);
			$src = str_replace('_', ' ', $src);
		} else {
			$src = '';
		}

		if( is_numeric($imageID) ) {
			$attachment = wp_prepare_attachment_for_js($imageID);

			if( is_array($attachment) ) {
				$content = str_replace('%media_title', $attachment['title'], $content );
				$content = str_replace('%media_alt', $attachment['alt'], $content );
				$content = str_replace('%media_caption', $attachment['caption'], $content );
				$content = str_replace('%media_description', $attachment['description'], $content );
			}
		}

		$content = str_replace('%media_title', $post->post_title, $content );
		$content = str_replace('%media_alt', $post->post_title, $content );
		$content = str_replace('%media_caption', $post->post_title, $content );
		$content = str_replace('%media_description', $post->post_title, $content );

		$content = str_replace('%name', $src, $content );
		$content = str_replace('%title', $post->post_title, $content );
		$content = str_replace('%category', $cats, $content );
		$content = str_replace('%tags', $tags, $content );
		$content = str_replace('%desc', $post->post_excerpt, $content);

		if( $replace_val && $replace_val !=='') {
			 $content = $content . ' - ' . $replace_val;
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


			$thumb_flag		= (isset($this->settings['thumbnail_override'])		? $this->settings['thumbnail_override'] : 'no');
            $title_scheme   = (isset($this->settings['thumbnail_title_val'])	? $this->settings['thumbnail_title_val'] : '');
            $alt_scheme     = (isset($this->settings['thumbnail_alt_val'])		? $this->settings['thumbnail_alt_val'] : '');
            $yoast			= ( (isset($this->settings['yost_primary_cat_override'])) ? $this->settings['yost_primary_cat_override']  : 'no' );

         if ($thumb_flag == 'yes') {
         
			$attr['title'] = trim($this->convert_replacements($title_scheme, array('src' => $attr['src'],'yoast' => $yoast)));
			$attr['alt'] = trim($this->convert_replacements($alt_scheme,array('src' => $attr['src'],'yoast' => $yoast)));

         } 
         elseif($thumb_flag == 'no') 
         {
			if( empty($attr['alt']) ) {
				$attr['alt'] = trim($this->convert_replacements($alt_scheme,array('src' => $attr['src'],'yoast' => $yoast)));
			}
			if( empty($attr['title']) ) {
				$attr['title'] = trim($this->convert_replacements($title_scheme,array('src' => $attr['src'],'yoast' => $yoast)));
			}
         }

		$attr['alt']    = apply_filters('wpbsio-alt', $attr['alt']);
		$attr['title']  = apply_filters('wpbsio-title', $attr['title']);

		return $attr;
	}

	public function optimize_html( $content, $force_optimization = false )
	{
	    $replace_val = ( (isset($this->settings['replace_val'])) ? explode(",", $this->settings['replace_val'] ) : false );
	    $yoast = ( (isset($this->settings['yost_primary_cat_override'])) ? $this->settings['yost_primary_cat_override']  : 'no' );

		$is_optimization_allowed = $this->is_optimization_allowed([
			'post_id'       => get_the_ID(),
			'post_type'     => get_post_type(),
			'content'       => $content
		]);

		// Фильтр для $is_optimization_allowed
		$is_optimization_allowed = apply_filters( 'optimize_html__is_optimization_allowed', $is_optimization_allowed );

		// Check if content could be optimized and if optimization is allowed
		if( false === $is_optimization_allowed && true !== $force_optimization ) {
			return $content;
		}

		// Пропускаем пустой $content
        $content_trim = trim($content);
		if( empty($content_trim) ) {
		    return $content;
        }

		// check again if DOMDocument is really available in case of force_optimization setting
		if( ! is_a($this->document, 'DOMDocument') ) {
			return $content;
		}

        $encoding_declaration = sprintf('<?xml encoding="%s" ?>', $this->settings['encoding_charset']);

		// Optimize encoding
		if( function_exists('mb_convert_encoding') && $this->settings['encoding_mode'] != 'off' ) {
			//$content = @mb_convert_encoding( $content, 'utf-8', $this->settings['encoding'] );
			$content = @mb_convert_encoding( $content, 'HTML-ENTITIES', $this->settings['encoding'] );
		} else {
            $content = $encoding_declaration.$content;
        }

        //libxml_use_internal_errors(true);	
        @$this->document->loadHTML( $content ); // LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD

		if( ! $this->document ) return $content;

		/*
		 * WooCommerce settings
		 */
        if( function_exists('is_woocommerce') && is_woocommerce() ) {
            if( isset($this->settings['wc_override']) && $this->settings['wc_override'] =='on') {
                $wc_title	= $this->settings['title_val'];
                $wc_alt		= $this->settings['alt_val'];
            }
        }

		/*
		 * Optimize Figure
		 *
         * Recommendation by BasTaller
         * @url https://wordpress.org/support/topic/proposal-for-best-replacement/
         */
		$figTags = $this->document->getElementsByTagName('figure');

		if( $figTags->length ) {
			foreach ($figTags as $tag){
				$caption = $tag->nodeValue;
				$imgTags = $tag->getElementsByTagName('img');
				
			    if( empty($caption) ) {continue;}
				
				if( isset($this->settings['title_cap']) && $this->settings['title_cap'] =='yes') 
				{
					foreach ($imgTags as $tag) {
						$tag->setAttribute('title', $caption);
					}

				} else {
					foreach ($imgTags as $tag) {
						$figtitle = trim($tag->getAttribute('title'));
						$tag->setAttribute('title', $figtitle);
					}
				}
			}
		}

		// check for image tags
		$imgTags = $this->document->getElementsByTagName('img');

		// return $content if there are no image tags in $content
		if( $imgTags->length ) {

            // check all image tags
            $j=0;
            foreach ($imgTags as $tag) {
				
				$replace = ( (isset($replace_val)) ? $replace_val[$j]  : '' );
                $data_src = trim($tag->getAttribute('data-src'));
                $src = trim($tag->getAttribute('src'));

                if( !empty($data_src) ) {
                    $src = $data_src;
                }
                $imageID = $this->get_image_id($src);

                //var_dump( trim($tag->getAttribute('alt')) );
                //var_dump( trim($tag->getAttribute('title')) );

                /**
                 * Override Area
                 */
                if( isset($this->settings['alt_override']) && $this->settings['alt_override'] =='yes' ) {
                    $alt = trim($this->convert_replacements($this->settings['alt_val'],array('src' => $src,'image_id' => $imageID,'replace_val' => $replace,'yoast' => $yoast)));
                    $alt = apply_filters('wpbsio-alt', $alt);
                    $tag->setAttribute('alt', $alt);
				}
				elseif( isset($this->settings['alt_override']) && $this->settings['alt_override'] =='no' ) {
	                    $alt = trim($tag->getAttribute('alt'));
                    	$alt = apply_filters('wpbsio-alt', $alt);
	                if( empty($alt) ) {
	                    $alt = trim($this->convert_replacements($this->settings['alt_val'],array('src' => $src,'image_id' => $imageID,'replace_val' => $replace,'yoast' => $yoast)));
    	                $alt = apply_filters('wpbsio-alt', $alt);
        	            $tag->setAttribute('alt', $alt);
					}
                } else {
                    $alt = trim($tag->getAttribute('alt'));
                    $alt = apply_filters('wpbsio-alt', $alt);
                }

                if( isset($this->settings['title_override']) && $this->settings['title_override'] =='yes') {
                    $title = trim($this->convert_replacements($this->settings['title_val'],array('src' => $src,'image_id' => $imageID,'replace_val' => $replace,'yoast' => $yoast)));
                    $title = apply_filters('wpbsio-title', $title);
                    $tag->setAttribute('title', $title);
                }
                elseif( isset($this->settings['title_override']) && $this->settings['title_override'] =='no') {
                    $title = trim($tag->getAttribute('title'));
                    $title = apply_filters('wpbsio-title', $title);
                	if( empty($title) ) {
            	        $title = trim($this->convert_replacements($this->settings['title_val'],array('src' => $src,'image_id' => $imageID,'replace_val' => $replace,'yoast' => $yoast)));
        	            $title = apply_filters('wpbsio-title', $title);
    	                $tag->setAttribute('title', $title);
		                }
                } else {
                    $title = trim($tag->getAttribute('title'));
                    $title = apply_filters('wpbsio-title', $title);
                }
            $j++;
            }

        }

		$return = $this->document->saveHTML();
		$return = str_replace($encoding_declaration, '', $return);
		$return = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $return));

		return $return;
	}


}
