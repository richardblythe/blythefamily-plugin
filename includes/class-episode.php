<?php

class BF_Episode extends Unity3_Post_Group {

	const POST_TYPE     = 'blythe_episode';
	private $header_img;

	function __construct() {

		parent::__construct( self::POST_TYPE, __('Episode'), __('Episodes'), 'Adds episodes from the Blythe Family YouTube channel' );

		$this->mergeSettings( array(
			'post' => array(
				'supports' => array( 'title', 'author' )
			),
			'drag_sort_posts' => false,
			'group_rewrite' => array( 'base' => 'episodes' ),
		));

		add_action( 'registered_post_type', array(&$this, 'block_template'), 100, 2 );
		add_filter( 'wp_insert_post_data' , array( &$this, 'insert_post_data') , '100', 2 );
		add_action('acf/save_post', array( $this, 'acf_save_post'), 20 );

		// Declare theme compatible with YouTube Video Import plugin
		// https://wpythub.com/
		add_filter( 'cbc_compatibility', '__return_true' );
		add_filter( 'cbc_compatibility_post_type', function() { return 'blythe_episode'; } );
		add_filter( 'cbc_compatibility_taxonomy', function() { return 'blythe_episode_group'; } );
		add_filter( 'cbc_compatibility_tag_taxonomy', function() { return 'blythe_episode_tag'; } );
		add_filter( 'cbc_compatibility_url_meta', function() { return 'youtube_url'; } );
		add_filter( 'cbc_compatibility_image_meta', function() { return '_thumbnail_id'; } );

		add_filter('request', array(&$this, 'add_to_podcast_feed'), 1000 );

		add_filter( 'get_site_icon_url', function($url, $size, $blog_id) {

			if (is_feed( 'podcast' )) {
				return null;
			}

			return $url;

		}, 100, 3);
	}

	function Init()
	{
		parent::Init();
		add_post_type_support( self::POST_TYPE, 'custom-fields' );
		add_post_type_support( self::POST_TYPE, 'genesis-singular-images' );
		add_post_type_support( self::POST_TYPE, 'genesis-entry-meta-before-content' );
		//get_post_type(), 'genesis-entry-meta-before-content'

//		if ( $post_type_object = get_post_type_object( self::POST_TYPE ) ) {
//
//			$post_type_object->template = array(
//				array( 'core/paragraph', array(
//					'placeholder' => 'Add episode description, scripture reading...',
//				) ),
//				array( 'core/paragraph', array(
//					'placeholder' => 'Any information that will only show in the podcast feed...',
//				) ),
//			);
//			$post_type_object->template_lock = 'all';
//
//		}


	}

	/**
	 * add custom post types to main rss feed
	 **/
	/**
	 * add custom post types to podcast feed
	 **/
	function add_to_podcast_feed($qv) {

		if ( isset($qv['feed']) && 'podcast' == $qv['feed'] ) {
			$qv['post_type'] = array('post', self::POST_TYPE);
		}
		return $qv;
	}

	function block_template( $post_type, $post_type_object ) {
		if ( self::POST_TYPE == $post_type) {
			$post_type_object->template = array(
				array( 'core/paragraph', array(
					'placeholder' => 'Add episode description...',
				) ),
				array( 'acf/blythe-podcast-notes', array(
					'placeholder' => 'Add episode description...',
				) ),
			);
		}

	}


	function insert_post_data( $data , $postarr ) {
		if(self::POST_TYPE == $data['post_type']) {
			//Remove YouTube "Devotion -" title references
			$data['post_title'] = str_replace("Devotion -", "", $data['post_title']);
			$data['post_title'] = str_replace("Devotion-", "", $data['post_title']);


			$data['post_title'] = trim( $data['post_title'] ); //remove trailing white spaces
			$data['post_name'] = sanitize_title($data['post_title']);
		}
		return $data;
	}

	function acf_save_post( $post_id ) {

		if ( self::POST_TYPE ==  get_post_type( $post_id ) ) {

			$acf_date = get_field( 'publish_date', $post_id);
			$my_post = array();
			$my_post['ID'] = $post_id;
			$my_post['post_date'] = $acf_date;
            $post = wp_update_post( $my_post );
        }

	}

}

////*************************
////       Register
////*************************
unity3_modules()->Register(new BF_Episode());