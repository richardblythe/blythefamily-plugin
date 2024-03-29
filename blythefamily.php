<?php
/*
    Plugin Name: Blythe Family
    Plugin URI: http://www.blythefamily.com/
    Description: Structural components for the Blythe Family website
    Version: 1.10.12
    Author: Richard Blythe
    Author URI: http://unity3software.com/richardblythe
    GitHub Plugin URI: https://github.com/richardblythe/blythefamily-plugin
 */
class BF {
    public static $ver, $dir, $url, $assets_url, $vendor_url, $blank_img, $in_header;
    function __construct() {

    	$debug = (defined('WP_DEBUG') && true === WP_DEBUG);

        BF::$dir = plugin_dir_path( __FILE__ );
        BF::$url = plugin_dir_url( __FILE__ );
	    BF::$assets_url = BF::$url . 'assets';
        BF::$vendor_url = BF::$url  . 'vendor';

        $this->initialize();
    }

    function initialize() {

    	// WP Rocket Integration
	    require_once (BF::$dir . 'includes/wp-rocket.php');
	    register_activation_hook( __FILE__, array($this, 'plugin_activate'));
	    register_deactivation_hook( __FILE__, array($this, 'plugin_deactivate') );

        // Save fields in functionality plugin
        add_filter( 'acf/settings/save_json', array( $this, 'get_local_json_path' ) );
        add_filter( 'acf/settings/load_json', array( $this, 'add_local_json_path' ) );

        add_filter( "theme_mod_header_image", array( $this, 'custom_header' ) );

		add_action( 'wp_enqueue_scripts', array( $this,'custom_stylesheet'), 100 );
	    add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin'), 100);
		add_filter('unity3_custom_template_path', function ( $paths ){
            $paths[] = BF::$dir . 'templates';
            return $paths;
        });

//	    add_filter('taxonomy_template', function ( $template, $type, $template_names ){
//		    return BF::$dir . 'templates/archive-blythe_episode.php';
//	    }, 100, 3);

	    add_filter( 'upload_mimes', array( &$this, 'allowed_mime_types' ), 100, 1 );
	    add_filter( 'as3cf_allowed_mime_types', array( &$this, 'allowed_mime_types' ), 100, 1 );

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

	function plugin_activate() {
		blythe_wprocket_activate();
		do_action('blythe_plugin_activate');
	}

	function plugin_deactivate() {
		blythe_wprocket_deactivate();
		do_action('blythe_plugin_deactivate');
	}

    function load_modules() {
        require_once (BF::$dir . 'includes/class-episode.php');
	    require_once (BF::$dir . 'includes/class-episode-transcription-editor.php');

        require_once (BF::$dir . 'includes/class-lyrics.php');
	    require_once (BF::$dir . 'includes/class-schedule.php');
	    require_once (BF::$dir . 'includes/shortcodes.php');
	    require_once (BF::$dir . 'includes/woocommerce.php');

	    require_once (BF::$dir . 'includes/powerpress.php');
	    require_once (BF::$dir . 'includes/sheet-music-requests.php');

	    wp_register_script('blythe-editor-script', BF::$url . 'build/index.js', null, filemtime( BF::$dir . 'build/index.js' ));
	    wp_register_style( 'blythe-editor-style', BF::$url . 'build/index.css', null, filemtime( BF::$dir . 'build/index.css' ) );
	    wp_register_style( 'blythe-style', BF::$url . 'build/style-index.css', null, filemtime( BF::$dir . 'build/style-index.css' ));

	    //Blocks
	    require_once ( BF::$dir . 'includes/blocks/podcast-notes.php' );
	    register_block_type_from_metadata(  BF::$dir . 'src/blocks/lyrics-section/block.json');
	    register_block_type_from_metadata(  BF::$dir . 'src/blocks/episode-info/block.json');
    }

    function allowed_mime_types() {
	    return array(
		    //Images
		    'jpg|jpeg|jpe' => 'image/jpeg',
		    'gif' => 'image/gif',
		    'png' => 'image/png',
		    'ico' => 'image/x-icon',

		    // Audio formats.
		    'mp3|m4a|m4b' => 'audio/mpeg',
		    'ogg|oga' => 'audio/ogg',

		    //Documents
		    'pdf' => 'application/pdf',
		    "csv" => "text/csv",

		    //Videos
		    'mpeg|mpg|mpe' => 'video/mpeg',
		    'mp4|m4v' => 'video/mp4',
	    );
    }

    function override_post_hero_featured($thumbnail_id, $object_id, $meta_key) {
	    if ( self::$in_header && '_thumbnail_id' == $meta_key) {

		    //any post, page, or cpt that wishes the hero to be the default set in: Customize -> Header Media
		    $use_default_hero = array( 'unity3_gallery', 'product', 'blythe_episode' );
		    if ( in_array( get_post_type( $object_id ), $use_default_hero ) ) {
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

	public function enqueue_admin() {
		wp_enqueue_style('blythe-editor-style');
		wp_enqueue_script(
			'blythe-admin-transcription',
			BF::$url . '/src/scripts/admin/audio_transcription.js',
			array('jquery'),
			filemtime( BF::$dir . '/src/scripts/admin/audio_transcription.js'),
			true
		);
	}

	public function my_custom_format_enqueue_assets_editor() {
		//wp_enqueue_script( 'blythefamily-admin-js' );
	}

	public function custom_stylesheet() {

//        wp_enqueue_style(
//            'blythefamily-style',
//            BF::$assets_url . '/dist/styles/blythe-studio-pro.css', [genesis_get_theme_handle()], BF::$ver
//        );

        //fontawesome
        wp_enqueue_script( 'font-awesome', 'https://kit.fontawesome.com/0e9ba0daf2.js', array(), '1.0.0', true );

        //dequeue GetWid's fontawesome
        wp_dequeue_style( 'fontawesome-free'  );

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
//if ( ! function_exists('write_log')) {
//	function write_log ( $log )  {
//		if ( is_array( $log ) || is_object( $log ) ) {
//			error_log( print_r( $log, true ) );
//		} else {
//			error_log( $log );
//		}
//	}
//}