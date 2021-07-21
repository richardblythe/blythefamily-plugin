<?php

class BF_Episode extends Unity3_Post_Group {

	const POST_TYPE     = 'blythe_episode';
	const GROUP_TAX     = 'blythe_episode_group';
	const GROUP_TERM_AHT = 'at-his-table';

	private $header_img;

	function __construct() {

		parent::__construct( self::POST_TYPE, __('Episode'), __('Episodes'), 'Adds episodes from the Blythe Family YouTube channel' );

		$this->mergeSettings( array(
			'post' => array(
				'supports' => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'custom-fields' )
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

		add_filter( 'post_row_actions', array(&$this, 'modify_list_row_actions'), 10, 2 );

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

		add_shortcode('blythe_pod_subscribe', 'do_blythe_pod_subscribe_shortcode');

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
				array( 'blythe/episode-info' ),
				array( 'acf/blythe-podcast-notes', array(
					'placeholder' => 'Add episode description...',
				) ),
			);
		}

	}


	function modify_list_row_actions( $actions, $post ) {
		// Check for your post type.
		if ( $post->post_type == BF_Episode::POST_TYPE ) {

			$meta = get_post_meta( $post->ID, 'audio_transcription', true );
			$status = get_post_meta( $post->ID, 'audio_transcription_status', true );
			$status = 'private' === $status ? 'Private' : 'Public';

			$actions = array(
				'transcription' => sprintf( '<a href="%1$s">%2$s</a>',
					$meta ? esc_url( admin_url('admin.php?page=aht-transcription&id=' . $post->ID) ) : '#',
					( $meta ? ( 'Edit Transcription - ' . $status ) : 'No Transcription' )
			));
//			unset( $actions['trash'] );
//			unset( $actions['inline hide-if-no-js'] );
//			unset( $actions['view'] );
		}

		return $actions;
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

function do_blythe_pod_subscribe_shortcode() {
	return
	'<div class="podcast-subscribe clearfix"><span class="subscribe-intro">' .
		__('You can also listen on:') . '</span>' .
		'<a href="https://podcasts.apple.com/us/podcast/at-his-table/id1538816843?itsct=podcast_box&itscg=30200"><i class="fas fa-podcast"></i>Apple Podcasts</a>' .
		'<a href="https://www.google.com/podcasts?feed=aHR0cHM6Ly9ibHl0aGVmYW1pbHkuY29tL2ZlZWQvcG9kY2FzdC8%3D"><i class="google-podcast-logo"></i>Google Podcasts</a>' .
		'<a href="https://open.spotify.com/show/4qlHv7H1mEBY8N1TJdqnmo"><i class="fab fa-spotify"></i>Spotify</a>' .
	'</div>';
}

function blythe_podcast_audio($metadata, $object_id, $meta_key, $single ){

	if ( isset( $meta_key ) && 'blythe_podcast_audio' == $meta_key ){

		$enclosure = get_post_meta($object_id, 'enclosure', true);
		$MetaParts = explode("\n", $enclosure, 4);

		if( count($MetaParts) > 0 ) {
			$metadata = array( trim($MetaParts[0]) );
		}
	}

	// Return
	return $metadata;

}

add_filter( 'get_post_metadata', 'blythe_podcast_audio', 100, 4 );

//filter the posts in the podcast audio download page
function blythe_filter_podcast_audio_posts( $post_args, $attributes ) {

	if ( isset( $attributes['customFields'] ) && strpos( $attributes['customFields'], 'blythe_podcast_audio' ) ) {
		$post_args['date_query'] = array(
			array(
				'before' => '2 weeks ago'
			)
		);
	}

	return $post_args;

}

add_filter( 'ptam_custom_post_types_query', 'blythe_filter_podcast_audio_posts',  100, 2);

require_once ( BF::$dir . '/includes/transcribe-options-page.php');