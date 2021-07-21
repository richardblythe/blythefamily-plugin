<?php

add_action('acf/init', 'my_acf_op_init');
function my_acf_op_init() {

	// Check function exists.
	if( function_exists('acf_add_options_page') ) {

		// Register options page.
		$option_page = acf_add_options_sub_page(array(
			'parent_slug'   => 'tools.php',
			'page_title'    => __('Transcription'),
			'menu_title'    => __('Transcription'),
			'menu_slug'     => 'aht-transcription',
			'capability'    => 'edit_posts',
			'redirect'      => false
		));
	}

	$post = get_post( intval( isset($_GET['aht_post_id']) ? $_GET['aht_post_id'] : -1 ) );
	$found_correct_post = (
		  $post &&
          $post->post_type == BF_Episode::POST_TYPE &&
		  has_term(BF_Episode::GROUP_TERM_AHT, BF_Episode::GROUP_TAX, $post )
	);

	$episode_title = null;
	$prevBtn = null;
	$nextBtn = null;
	$header_html = null;

	$my_query = new WP_Query( array(
		'post_type' => BF_Episode::POST_TYPE,
		'posts_per_page' => 1,
		'post__in' => $found_correct_post ? array( $post->ID ) : array(),
		'tax_query' => array(
			array(
				'taxonomy' => BF_Episode::GROUP_TAX,
				'field'    => 'slug',
				'terms'    => BF_Episode::GROUP_TERM_AHT,
			),
		),
	) );
	if ( $my_query->have_posts() ) {
		if ( $my_query->have_posts() ) {
			$my_query->the_post();

			$post = get_post();
			$_REQUEST['aht_post_id'] = $post->ID;

			$episode_title = '<h3>' . get_the_title() .'</h3>';

			$prev_post = get_previous_post(true, null, BF_Episode::GROUP_TAX );
			$next_post = get_next_post( true, null, BF_Episode::GROUP_TAX );

			$prev_url = $prev_post ? admin_url('admin.php?page=aht-transcription&id=' . $prev_post->ID) : '#';
			$next_url = $next_post ? admin_url('admin.php?page=aht-transcription&id=' . $next_post->ID) : '#';

			$prevBtn = '<a class="button' . ($prev_url == '#' ? ' disabled' : '' ) . '" href="' . $prev_url . '" title="Previous Episode">Prev</a>';
			$nextBtn = '<a class="button' . ($next_url == '#' ? ' disabled' : '' ) . '" href="' . $next_url . '" title="Next Episode">Next</a>';

			$hidden = '<input type="hidden" name="aht_post_id" value="' . $post->ID . '" />';

			$header_html = "{$episode_title} {$prevBtn} {$nextBtn} {$hidden}";
		}

	} else {
		$post = null;
	}

	wp_reset_postdata();
	if ( !$post ) {
		return;
	}


	acf_add_local_field_group( array(
		"key" => "blythe_transcription_group",
		"title" => "Episode Transcription",
        "fields" => array(
			array(
		        "key" => "blythe_transcription_field_header",
	            "label" => "",
	            "name" => "",
	            "type" => "message",
	            "instructions" => "",
	            "required" => 0,
	            "conditional_logic" => 0,
	            "message" => $header_html,
	            "new_lines" => "",
	            "esc_html" => 0
			),
	        array(
	            "key" => "field_60f0afb7aeeb8",
	            "label" => "Transcription Visibility",
	            "name" => "transcription_visibility",
	            "type" => "select",
	            "instructions" => "Set as Private if you don't want the public to see your changes yet.",
	            "required" => 0,
	            "conditional_logic" => 0,
	            "choices" => array(
					"private" => "Private",
	                "public" => "Public"
	            ),
	            "default_value" => "private",
	            "allow_null" => 0,
	            "multiple" => 0,
	            "ui" => 1,
	            "ajax" => 0,
	            "return_format" => "value",
	            "placeholder" => ""
			),
	        array(
		        "key" => "field_60f060509f68a",
	            "label" => "Content",
	            "name" => "transcription_content",
	            "type" => "wysiwyg",
	            "instructions" => "Make sure to save any changes",
	            "required" => 0,
	            "conditional_logic" => 0,
	            "default_value" => '',
	            "tabs" => "visual",
	            "toolbar" => "full",
	            "media_upload" => 0,
	            "delay" => 0
			)
		),
        "location" => array(
			array(
	            array(
		            "param" => "options_page",
	                "operator" => "==",
	                "value" => "aht-transcription"
	            )
	        )
		),
	));
}

function aht_process_value( $value, $key, $updating = false) {
	$aht_post_id = intval( isset($_REQUEST['aht_post_id']) ? $_REQUEST['aht_post_id'] : 0 );

	if( $aht_post_id ) {
		if ( $updating ) {
			update_post_meta( $aht_post_id, $key, $value );
			$value = '';
		} else {
			$value = get_post_meta( $aht_post_id, $key, true);
		}

	}
	return $value;
}

function aht_transcription_override_load( $value, $post_id, $field ) {
	if ( in_array( $field['name'], array( 'transcription_content', 'transcription_visibility' ) ) ) {
		return aht_process_value( $value, $field['name'], false );
	}

	return $value;
}

// Apply to all fields.
add_filter('acf/load_value', 'aht_transcription_override_load', 10, 3);

function aht_transcription_override_save( $value, $post_id, $field, $original ) {
	if ( in_array( $field['name'], array( 'transcription_content', 'transcription_visibility' ) ) ) {
		return aht_process_value( $value, $field['name'], true );
	}

	return $value;
}

// Apply to all fields.
add_filter('acf/update_value', 'aht_transcription_override_save', 100, 4);