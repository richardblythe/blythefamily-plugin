<?php

class BF_Lyrics {

	const POST_TYPE     = 'lyrics';
	const TAX_ALBUM  = 'album';
	const TAX_GLOSSARY  = 'glossary';
	const TAX_TOPIC     = 'topic';
	const TRANS_ARCHIVE = 'bf_lyrics_archive_alphabet';
	private $header_img;

	function __construct() {
		add_action( 'init', array(&$this, 'init') );
		//add_filter( 'get_the_archive_title', array(&$this, 'archive_title') );
//		add_filter( 'post_type_archive_title', array(&$this, 'archive_title'), 99, 2);

		if (is_admin()) {

			add_action( 'save_post', array(&$this, 'save') );

		} else {

			add_filter( 'template_include', array(&$this, 'template_include'), 99, 1 );
			add_filter( "theme_mod_header_image", array(&$this, 'header_image' ), 10, 1 );
			add_filter( 'single_term_title',  array(&$this, 'single_term_title'), 99, 1 );
			add_action( 'genesis_before', array(&$this, 'genesis_init'), 1 );
			add_filter( 'genesis_post_type_crumb', array(&$this, 'breadcrumb'), 99, 2);// $crumb, $this->args );
			add_filter( 'genesis_tax_crumb', array(&$this, 'breadcrumb'), 99, 2);

			add_filter('wp_title', array(&$this, 'filter_pagetitle') );
		}

	}

	function init() {

		if ( function_exists('unity3_register_post_type')) {

			unity3_register_taxonomy(BF_Lyrics::TAX_ALBUM, BF_Lyrics::POST_TYPE, 'Album', 'Albums', array(
				'hierarchical' => false,
				'rewrite' => array(
					'slug' => BF_Lyrics::POST_TYPE . '/' . BF_Lyrics::TAX_ALBUM,
					'query_var'    => true,
					'with_front' => false
				),
			));

			//register taxonomy FIRST so that the rewrite rules are applied correctly
			unity3_register_taxonomy(BF_Lyrics::TAX_TOPIC, BF_Lyrics::POST_TYPE, 'Topic', 'Topics', array(
				'hierarchical' => false,
				'rewrite' => array(
					'slug' => BF_Lyrics::POST_TYPE . '/' . BF_Lyrics::TAX_TOPIC,
					'query_var'    => true,
					'with_front' => false
				),
			));
			unity3_register_taxonomy(BF_Lyrics::TAX_GLOSSARY, BF_Lyrics::POST_TYPE, 'Glossary', 'Glossary', array(
				'hierarchical' => false,
				'rewrite' => array(
					'slug' => BF_Lyrics::POST_TYPE . '/' . BF_Lyrics::TAX_GLOSSARY,
					'query_var'    => true,
					'with_front' => false,
				),
                'show_ui' => false,
                'show_tagcloud' => false,
                'show_in_nav_menus' => false,
			));

			unity3_register_post_type(
				BF_Lyrics::POST_TYPE,
				'Lyrics',
				'Lyrics',
				array(
					'show_in_menu' => true,
					'menu_icon' => 'dashicons-playlist-audio',
					'menu_position' => 9,
					'show_in_rest' => true,
					'rewrite' => array( 'slug' => 'lyrics', 'with_front' => true ),
				),
				false
			);




		}

		//run lyrics database initialization code
		if ( false === get_transient( 'bf_lyrics_init' ) ) {

			$alphabet = array();

			$posts = get_posts(array(
				'post_type'   => BF_Lyrics::POST_TYPE,
				'numberposts' => -1
			));

			foreach( $posts as $p ) {
				//set term as first letter of post title, lower case
				wp_set_post_terms( $p->ID, strtolower(substr($p->post_title, 0, 1)), BF_Lyrics::TAX_GLOSSARY );
			}

			set_transient( 'bf_lyrics_init', 'true' );
		}
	}


	/* When the post is saved, saves our custom data */
	function save( $post_id ) {
		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		//check location (only run for lyrics)
		if ($_POST['post_type'] == BF_Lyrics::POST_TYPE)
			return $post_id;

		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		//set term as first letter of post title, lower case
		wp_set_post_terms( $post_id, strtolower(substr($_POST['post_title'], 0, 1)), BF_Lyrics::TAX_GLOSSARY );

		//delete the transient that is storing the alphabet letters
		delete_transient( BF_Lyrics::TRANS_ARCHIVE);
	}

	function template_include( $template ) {
		if ( is_post_type_archive( BF_Lyrics::POST_TYPE ) ||
			 $this->is_lyrics_tax() ) {
			$template = BF::$dir . '/templates/lyrics.php';
		}

		return $template;
	}

	function genesis_init() {
		remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_headline', 10, 3 );
		add_action( 'genesis_archive_title_descriptions', array(&$this, 'archive_headings_headline'), 10, 3 );
        remove_post_type_support( self::POST_TYPE, 'genesis-entry-meta-before-content' );
	}

	function filter_pagetitle($title) {
		//check if its a blog post
		if (!is_single())
			return $title;

		//if you get here then its a blog post so change the title
		global $wp_query;
		if (isset($wp_query->post->post_title)){
			return $wp_query->post->post_title;
		}

		//if wordpress can't find the title return the default
		return $title;
	}


	function header_image( $url ) {

//		if ( is_singular( BF_Lyrics::POST_TYPE ) ||
//		     is_post_type_archive( BF_Lyrics::POST_TYPE ) ||
//		     $this->is_lyrics_tax()
//		   ) {
//
//            $queried_object = get_queried_object();
//            $term_id        = $queried_object->term_id;
//            if ( $image_id = get_option( 'options_bf_settings_lyrics_header_img', true ) ) {
//                $image = wp_get_attachment_image_src( $image_id, 'full' );
//                $url = $image[0];
//            }
//		}

		return $url;
	}


	function single_term_title($title) {

		if ( $this->is_lyrics_tax() ) {
			$term = get_queried_object();
			$title = 'Lyrics / ' . $this->captitalize_single($term->name);
		}
		return $title;
	}


	function breadcrumb($crumb, $args) {
		if ( is_post_type_archive(BF_Lyrics::POST_TYPE) ) {
			$crumb = 'All Lyrics';
		} else if ( $this->is_lyrics_tax() ) {
			$term = get_queried_object();
			// Build the breadcrumb
			$crumb = '<a href="' . get_post_type_archive_link( BF_Lyrics::POST_TYPE ) .
			         '">Lyrics</a>' .
			         $args['sep'] .
			         $this->captitalize_single($term->name);

			return $crumb;
		}

		return $crumb;
	}


	function archive_title( $title, $post_type ) {

		if( BF_Lyrics::POST_TYPE == $post_type ) {

			$title = 'My 123 Lyrics';

		}

		return $title;

	}

	function archive_headings_headline( $heading = '', $intro_text = '', $context = '' ) {

		if ( $this->is_lyrics_tax() ) {
			$heading = 'Lyrics / ' . $this->captitalize_single($heading);
		}

		//Default Genesis Code:
		if ( $context && $heading ) {
			printf( '<h1 %s>%s</h1>', genesis_attr( 'archive-title' ), esc_html( wp_strip_all_tags( $heading ) ) );
		}

	}

	function captitalize_single( $text ) {
		return (1 == strlen($text) ? strtoupper( $text ) : text );
	}

	function is_lyrics_tax() {
		return is_tax( BF_Lyrics::TAX_GLOSSARY) || is_tax( BF_Lyrics::TAX_TOPIC );
	}
}

new BF_Lyrics();





function blythe_get_lyrics_by_views( $max_results ) {
	bf_analytics();
}

function blythe_lyrics_meta() {
	$album_id = 1;// get_post_meta(get_the_ID(), 'album_id', true );
	$album_html = $album_id ? '<a class="fa icon-music-note-streamline fa-2x" href="'. get_permalink($album_id) .'">View Album</a>' : '';
	//
	$track_id = 1; // get_post_meta(get_the_ID(), 'sound_track_id', true );
	$track_html = $track_id ? '<a class="fa icon-micro-record-streamline fa-2x" href="'. get_permalink($track_id) .'">Sound Track Available!</a>' : '';
	//
	//for some weird reason, ACF is not working with a field key of: sheet_music_id
	$sheet_music_id = 1; // get_post_meta(get_the_ID(), 'sheet_music', true );
	$sheet_music_html = '';
	if ($sheet_music_id) {
		$sheet_music_html = '<a class="fa icon-book-read-streamline fa-2x" href="'. get_permalink($sheet_music_id) .'">Sheet Music Available!</a>';
	} else {
		$song_title = esc_attr( get_the_title() );
		$sheet_music_html = '<a class="fa icon-book-read-streamline fa-2x request" href="'. get_permalink( get_page_by_path('sheet-music-request') ) . '?song_title=' . $song_title .'">Request Sheet Music</a>';
	}
	//

	echo "<div class='lyrics-meta'>
				<a class='one-third first' href=\"#\"><i class=\"fa icon-music-note-streamline fa-2x\"></i> Album</a>
				<a class='one-third' href=\"#\"><i class=\"fa icon-micro-record-streamline fa-2x\"></i> Track Available!</a>
				<a class='one-third' href=\"#\"><i class=\"fa icon-book-read-streamline fa-2x\"></i> Music Available!</a>
			</div>";
//	$result = "<div class='lyrics-meta'>{$album_html}{$track_html}{$sheet_music_html}</div>";
}
