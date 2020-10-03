<?php


/**
 * Archive Post Class
 *
 * Breaks the posts into three columns
 * @link http://www.billerickson.net/code/grid-loop-using-post-class
 *
 * @param array $classes
 * @return array
 */
function be_archive_post_class($classes)
{

    // Don't run on single posts or pages
    if (is_singular())
        return $classes;

    $classes[] = 'one-half';
    global $wp_query;
    if (0 == $wp_query->current_post || 0 == $wp_query->current_post % 3)
        $classes[] = 'first';
    return $classes;
}

add_filter('post_class', 'be_archive_post_class');


add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar', 99 );

function cd_change_genesis_sidebar() {
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); //remove the default genesis sidebar
	add_action( 'genesis_sidebar', 'cd_do_sidebar' ); //add an action hook to call the function for my custom sidebar
}
add_action('get_header','cd_change_genesis_sidebar');

//Function to output my custom sidebar
function cd_do_sidebar() {

	add_filter( 'wp_generate_tag_cloud_data', 'bf_lyrics_glossary_tag_cloud_data', 99, 1 );

	genesis_widget_area( 'lyrics', array(
		'before' => '<div class="lyrics widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );

}

function bf_lyrics_glossary_tag_cloud_data( $tags_data ) {

	//only run once
	remove_filter( 'wp_generate_tag_cloud_data', 'bf_lyrics_glossary_tag_cloud_data');

	foreach ( $tags_data as &$data ) {
		$data['name'] = ( 1 == strlen($data['name']) ? strtoupper($data['name']) : $data['name'] );
		$data['font_size'] = 16;

		if ( $term = get_term($data['id']) ) {
			$data['class'] .= " tax-{$term->taxonomy}";
		}

//			'id'              => $tag_id,
//			'url'             => '#' != $tag->link ? $tag->link : '#',
//			'role'            => '#' != $tag->link ? '' : ' role="button"',
//			'name'            => $tag->name,
//			'formatted_count' => $formatted_count,
//			'slug'            => $tag->slug,
//			'real_count'      => $real_count,
//			'class'           => 'tag-cloud-link tag-link-' . $tag_id,
//			'font_size'       => $args['smallest'] + ( $count - $min_count ) * $font_step,
//			'aria_label'      => $aria_label ? sprintf( ' aria-label="%1$s (%2$s)"', esc_attr( $tag->name ), esc_attr( $formatted_count ) ) : '',
//			'show_count'      => $args['show_count'] ? '<span class="tag-link-count"> (' . $real_count . ')</span>' : '',
//		);
	}

	return $tags_data;
}


function unity3_before_lyrics() {
	add_filter( 'get_search_form', 'unity3_lyrics_search_form' );
	get_search_form();
    remove_filter( 'get_search_form', 'unity3_lyrics_search_form' );
}
add_action('genesis_before_content_sidebar_wrap', 'unity3_before_lyrics');


function unity3_lyrics_search_form( $form ) {
	$form = '<form role="search" method="get" id="searchform" class="lyrics search-form" action="' . home_url( '/' ) . '" >
	<div><p class="screen-reader-text" for="s">' . __( 'Search Lyrics for:' ) . '</p>
	<input type="search" value="' . get_search_query() . '" name="s" id="s" placeholder="Search all lyrics" />
	<input type="hidden" value="' . BF_Lyrics::POST_TYPE . '" name="post_type" id="post_type" />
	</div>
	</form>';

	return $form;
}


//remove_action('genesis_loop', 'genesis_do_loop');
//add_action('genesis_loop', 'child_do_loop');


// remove_action( 'genesis_entry_content', 'genesis_do_post_image');
// remove_action( 'genesis_entry_content', 'genesis_do_post_content');
// remove_action( 'genesis_entry_content', 'genesis_do_post_content_nav');
// remove_action( 'genesis_entry_content', 'genesis_do_post_permalink');

// remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open');
// remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close');
// remove_action( 'genesis_entry_footer', 'genesis_post_meta');


// function bf_lyrics_archive_info() {
// 	// NEW CODE!
// 	$album_id = get_post_meta(get_the_ID(), 'album_id', true );
// 	$song_number = get_post_meta(get_the_ID(), 'song_number', true );

// 	return '<a class="product-link" href="' . get_permalink($album_id) . '">' .
// 	       '<span class="categories">' . get_the_title($album_id) . '</span> &rarr; '.
// 	       '<span class="track-num">Track ' . $song_number . '</span>'.
// 	       '</a>';
// }
// add_filter( 'genesis_post_info', 'bf_lyrics_archive_info');


// function be_archive_post_class( $classes ) {
// 	global $wp_query;
// 	$classes[] = 'one-half';
// 	if( 0 == $wp_query->current_post || 0 == $wp_query->current_post % 2 )
// 		$classes[] = 'first';
// 	return $classes;
// }
// add_filter( 'post_class', 'be_archive_post_class' );



function child_do_loop() {
	// $args = array(
	//         'post_type' => 'lyrics', // enter your custom post type
	//         'orderby' => 'title',
	//         'order' => 'ASC',
	//         'posts_per_page' => '10',
	//         'paged' => get_query_var( 'paged' )
	// );
	// genesis_custom_loop( $args );
	// echo '<div class="one-half">';
	// echo do_shortcode('[a-z-listing display="posts" post-type="lyrics"]');
	// echo '</div>';

	genesis_widget_area( 'lyrics-area', array(
		'before' => '<div class="lyrics-area widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );
}



// /** Remove Post Info */
// remove_action('genesis_before_post_content','genesis_post_info');
// remove_action('genesis_after_post_content','genesis_post_meta');

genesis();

