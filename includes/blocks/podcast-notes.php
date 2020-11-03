<?php
add_action('acf/init', 'my_register_blocks');
function my_register_blocks() {

	// check function exists.
	if( function_exists('acf_register_block_type') ) {

		// register a testimonial block.
		acf_register_block_type(array(
			'name'              => 'blythe_podcast_notes',
			'title'             => __('Podcast Notes'),
			'description'       => __('Only shows the contents to a podcast feed'),
			'render_callback'   => 'blythe_podcast_notes_render',
			'category'          => 'formatting',
            'post_types'        => array( BF_Episode::POST_TYPE ),
			'supports' => array(
				'align' => false,
			),
			'mode' => 'edit',
		));
	}
}

/**
 * Block Callback Function.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */
function blythe_podcast_notes_render( $block, $content = '', $is_preview = false, $post_id = 0 ) {

	//if we're not in the admin area or not generating a feed, get out of here.
    if ( !(is_admin() || is_feed()) )
        return;

    if ( is_admin() ) {
	    echo "<hr><h3 style='margin-bottom: 2px'>Podcast Only Notes</h3>";
	    echo "<span style='font-style: italic;'>This content is only shown in a podcast feed</span>";
    }

	// Create class attribute allowing for custom "className" and "align" values.
	$className = 'blythe-podcast-notes';
	if( !empty($block['className']) ) {
		$className .= ' ' . $block['className'];
	}
	if( !empty($block['align']) ) {
		$className .= ' align' . $block['align'];
	}

	// Load values

    //Todo Maybe add a checkbox selection that outputs default content such as links to YouTube channel, website...
    if ( $podcast_notes = get_field('podcast_notes') ) {
        echo $podcast_notes;
    }


//	$author = get_field('author') ?: 'Author name';
//	$role = get_field('role') ?: 'Author role';
//	$image = get_field('image') ?: 295;
//	$background_color = get_field('background_color');
//	$text_color = get_field('text_color');
}