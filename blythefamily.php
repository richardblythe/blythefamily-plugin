<?php
/*
    Plugin Name: Blythe Family
    Plugin URI: http://www.blythefamily.com/
    Description: Structural components for the Blythe Family website
    Version: 1.8
    Author: Richard Blythe
    Author URI: http://unity3software.com/richardblythe
    GitHub Plugin URI: https://github.com/richardblythe/blythefamily-plugin
 */
class BF {
    public static $ver, $dir, $url, $assets_url, $vendor_url, $blank_img, $in_header;
    private $min;
    function __construct() {

    	$debug = (defined('WP_DEBUG') && true === WP_DEBUG);

        //TODO replace time
	    BF::$ver = time(); //'1.0';
        BF::$dir = plugin_dir_path( __FILE__ );
        BF::$url = plugin_dir_url( __FILE__ );
	    BF::$assets_url = BF::$url . 'assets';
        BF::$vendor_url = BF::$url  . 'vendor';
	    BF::$blank_img = BF::$assets_url . '/images/blank.gif';

        $this->min = $debug ? '.min.' : '.';

        $this->initialize();
    }

    function initialize() {

        // Save fields in functionality plugin
        add_filter( 'acf/settings/save_json', array( $this, 'get_local_json_path' ) );
        add_filter( 'acf/settings/load_json', array( $this, 'add_local_json_path' ) );

        add_filter( "theme_mod_header_image", array( $this, 'custom_header' ) );

		add_action( 'wp_enqueue_scripts', array( $this,'custom_stylesheet'), 100 );
        add_filter('unity3_custom_template_path', function ( $paths ){
            $paths[] = BF::$dir . 'templates';
            return $paths;
        });

        //allow mp3s to be
         add_filter('upload_mimes', function( $types ) {
             return array_merge($types, array(
                 // Audio formats.
                 'mp3|m4a|m4b' => 'audio/mpeg',
//                 'ogg|oga' => 'audio/ogg',
             ));
         });


        add_action('unity3/modules/load', array( $this, 'load_modules' ) );

        add_action('wp_head', function (){
            self::$in_header = true;

            echo "<!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-176994444-1\"></script>
                <script>
                  window.dataLayer = window.dataLayer || [];
                  function gtag(){dataLayer.push(arguments);}
                  gtag('js', new Date());
                
                  gtag('config', 'UA-176994444-1');
                </script>";

        }, 0);
        add_action('genesis_after_header', function (){ self::$in_header = false; }, 0);

	    add_filter( "get_post_metadata", array( &$this, 'override_post_hero_featured'), 100, 3);
        add_filter( "default_post_metadata", array(&$this, 'get_default_metadata'), 100, 5);
    }

    function load_modules() {
        require_once (BF::$dir . 'includes/class-episode.php');
        require_once (BF::$dir . 'includes/class-lyrics.php');
	    require_once (BF::$dir . 'includes/woocommerce.php');
//        require_once (BF::$dir . 'includes/powerpress.php');
    }

    function override_post_hero_featured($thumbnail_id, $object_id, $meta_key) {
	    if ( self::$in_header && '_thumbnail_id' == $meta_key) {

		    //override woocommerce product page hero image with the one set in Customize -> Header Media
		    if ( 'product' == get_post_type($object_id) )  {
			    $thumbnail_id = false;
		    }
	    }

	    return $thumbnail_id;
    }

    function get_default_metadata( $value, $object_id, $meta_key, $single, $meta_type ) {

	    if ( self::$in_header && '_thumbnail_id' == $meta_key) {

	    	//any post, page, or cpt that wishes the hero to be the default set in: Customize -> Header Media
		    $use_default_hero = array( 'unity3_gallery', 'product' );
		    $post_type = get_post_type( $object_id );

		    if ( in_array( get_post_type( $object_id ), $use_default_hero ) ) {
			    $value = get_header_image();
		    }
	    }


        return $value;
    }


	public function custom_stylesheet() {

        wp_enqueue_style(
            'blythefamily-style',
            BF::$assets_url . '/dist/styles/blythe-studio-pro.css', [genesis_get_theme_handle()], BF::$ver
        );

        //fontawesome
        wp_enqueue_script( 'font-awesome', 'https://kit.fontawesome.com/0e9ba0daf2.js', array(), '1.0.0', true );

	}

	function custom_header( $default ) {

        if ( is_front_page() ) {
            return 'remove-header'; //smartslider has the home page
        } else if ( $module = unity3_modules()->Get(BF_Episode::POST_TYPE) ) {

            $term_id = false;

            if ( is_singular( BF_Episode::POST_TYPE )) {
                $terms = wp_get_post_terms( get_the_ID(), $module->GetTaxonomy());
                $term_id = $terms[0]->term_id;
            }
            else if ( is_tax( $module->GetTaxonomy() ) ) {
                $term_id = get_queried_object()->term_id;
            }

            //
            if ( $term_id && $img_id = get_term_meta( $term_id, '_thumbnail_id', true ) ) {
                $image = wp_get_attachment_image_src( $img_id, 'hero', false );
                $default = $image[0];
            }

        }

        return $default;
    }



    /**
     * Define where the local JSON is saved
     *
     * @return string
     */
    public function get_local_json_path() {
        return BF::$dir . 'acf-json';
    }

    /**
     * Add our path for the local JSON
     *
     * @param array $paths
     *
     * @return array
     */
    public function add_local_json_path( $paths ) {
        $paths[] = BF::$dir . 'acf-json';
        return $paths;
    }
}

new BF();
