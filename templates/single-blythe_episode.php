<?php

remove_action( 'studio_hero_section', 'studio_page_title', 10 );

function override_post_hero_image( $meta_value, $object_id, $meta_key ) {
    if ( '_thumbnail_id' == $meta_key && BF_Episode::POST_TYPE == get_post_type($object_id) && $module = unity3_modules()->Get(BF_Episode::POST_TYPE) ) {
        $terms = wp_get_post_terms( get_the_ID(), $module->GetTaxonomy());
        $term_id = $terms[0]->term_id;

        if ( $term_id && $img_id = get_term_meta( $term_id, '_thumbnail_id', true ) ) {
            $image = wp_get_attachment_image_src( $img_id, 'hero', false );
            $meta_value = $image[0];
        }
    }

    return $meta_value;
}
add_filter( "get_post_metadata", 'override_post_hero_image', 10, 3 );


remove_action( 'genesis_entry_content', 'genesis_do_singular_image', 8 );
// Move Entry Header
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
//
add_action( 'genesis_entry_content', 'genesis_entry_header_markup_open', 2 );
add_action( 'genesis_entry_content', 'genesis_do_post_title',3 );
add_action( 'genesis_entry_content', 'genesis_post_info', 4 );
add_action( 'genesis_entry_content', 'genesis_entry_header_markup_close', 5 );


add_action('genesis_attr_entry-content', function ( $attributes ){
    // add original plus extra CSS classes
	if ( has_term( 'at-his-table', 'blythe_episode_group' ) ) {
		$attributes['class'] .= ' one-third';
	}
    return $attributes;
});



add_action( 'genesis_before_entry_content', function() {

    $url = get_field('youtube');
//    echo do_shortcode('[embed width="123" height="456"]' . $url . '[/embed]');

    $res = array(
        'width'		=> 123,
        'height'	=> 456
    );

    // get emebed
    $embed = @wp_oembed_get( $url, $res );


    // try shortcode
    if( !$embed ) {

        // global
        global $wp_embed;


        // get emebed
        $embed = $wp_embed->shortcode($res, $url);

    }

    if ( $embed ) {

        $class_names = "";
	    if ( has_term( 'at-his-table', 'blythe_episode_group' ) ) {
	        $class_names = 'two-thirds first';
        }

        echo '<div class="' . $class_names .'"><div class="embed-container">' . $embed . '</div></div>';

    }
});

add_action( 'genesis_after_entry_content', function() {

    if ( has_term( 'at-his-table', 'blythe_episode_group' ) ) {
	    echo do_blythe_pod_subscribe_shortcode();
    }
});



genesis();