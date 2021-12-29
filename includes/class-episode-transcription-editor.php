<?php

class BF_Episode_Transcription_Editor  {

	private $player_html;
	const KEY_HEADER = 'bf_episode_transcription_editor__header123';
	const KEY_VISIBILITY = 'bf_episode_transcription_editor__visibility';
	const KEY_CONTENT = 'bf_episode_transcription_editor__content';
	const KEY_NO_JOB = 'bf_episode_transcription_editor__no_job';

	public function __construct() {
		// Apply to all fields.
		add_action( 'acf/init', array(&$this, 'acf_init') );
		add_filter('mejs_settings', function ($settings) {
			$settings['features'] = array('playpause','current','progress','duration','tracks','volume','fullscreen','skipback','jumpforward');
			return $settings;
		});
	}


	function acf_init() {

		add_filter( 'acf/update_value',  array(&$this, 'override_save'), 100, 4 );
		add_filter( 'acf/prepare_field',  array(&$this, 'prepare_field') );
		add_filter( 'acf/load_value', array(&$this, 'override_load'), 10, 3 );

		add_filter( 'acf/load_field/key=' . self::KEY_HEADER, function ($field){
			echo '<div class="acf-field">';
			echo $this->get_header_html();
			echo '</div>';
		} );

		// Register options page.
		$option_page = acf_add_options_sub_page( array(
			'parent_slug' => 'tools.php',
			'page_title'  => __( 'Transcription' ),
			'menu_title'  => __( 'Transcription' ),
			'menu_slug'   => 'aht-transcription',
			'capability'  => 'edit_posts',
			'redirect'    => false
		) );

		acf_add_local_field_group( array(
			"key"      => "blythe_simple_transcription_group",
			"title"    => "Episode Transcription",
			"fields"   => array(
				array(
					"key"               => self::KEY_HEADER,
					"label"             => "",
					"name"              => "",
					"type"              => "message",
					"instructions"      => "",
					"required"          => 0,
					"conditional_logic" => 0,
					"message"           => 'Hello',
					"new_lines"         => "",
					"esc_html"          => 0
				),
				array(
					"key"               => self::KEY_VISIBILITY,
					"label"             => "Transcription Visibility",
					"name"              => Unity3_Audio_Transcription::PM_VISIBILITY,
					"type"              => "select",
					"instructions"      => "Set as Private if you don't want the public to see your changes yet.",
					"required"          => 0,
					"conditional_logic" => 0,
					"choices"           => array(
						"private" => "Private",
						"public"  => "Public"
					),
					"default_value"     => "private",
					"allow_null"        => 0,
					"multiple"          => 0,
					"ui"                => 1,
					"ajax"              => 0,
					"return_format"     => "value",
					"placeholder"       => ""
				),
				array(
					"key"               => self::KEY_CONTENT,
					"label"             => "Content",
					"name"              => Unity3_Audio_Transcription::PM_CONTENT,
					"type"              => "wysiwyg",
					"instructions"      => "Make sure to save any changes",
					"required"          => 0,
					"conditional_logic" => 0,
					"default_value"     => '',
					"tabs"              => "visual",
					"toolbar"           => "full",
					"media_upload"      => 0,
					"delay"             => 0
				),
				array(
					"key"               => self::KEY_NO_JOB,
					"label"             => "",
					"name"              => "",
					"type"              => "message",
					"instructions"      => "",
					"required"          => 0,
					"conditional_logic" => 0,
					"message"           => '',
					"new_lines"         => "",
					"esc_html"          => 0
				),
			),
			"location" => array(
				array(
					array(
						"param"    => "options_page",
						"operator" => "==",
						"value"    => "aht-transcription"
					)
				)
			),
		) );

		//end init
	}


	function get_header_html() {
		$post               = get_post( intval( isset( $_GET['aht_post_id'] ) ? $_GET['aht_post_id'] : - 1 ) );
		$found_correct_post = (
			$post &&
			$post->post_type == BF_Episode::POST_TYPE &&
			has_term( BF_Episode::GROUP_TERM_AHT, BF_Episode::GROUP_TAX, $post )
		);

		$episode_title = null;
		$prevBtn       = null;
		$nextBtn       = null;
		$header_html   = null;

		$my_query = new WP_Query( array(
			'post_type'      => BF_Episode::POST_TYPE,
			'posts_per_page' => 1,
			'post__in'       => $found_correct_post ? array( $post->ID ) : array(),
			'tax_query'      => array(
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

				$post                    = get_post();
				$_REQUEST['aht_post_id'] = $post->ID;

				$episode_title = '<h3>' . get_the_title() . '</h3>';

				$prev_post = get_previous_post( true, null, BF_Episode::GROUP_TAX );
				$next_post = get_next_post( true, null, BF_Episode::GROUP_TAX );

				$prev_url = $prev_post ? admin_url( 'admin.php?page=aht-transcription&aht_post_id=' . $prev_post->ID ) : '#';
				$next_url = $next_post ? admin_url( 'admin.php?page=aht-transcription&aht_post_id=' . $next_post->ID ) : '#';

				$prevBtn = '<a class="button' . ( $prev_url == '#' ? ' disabled' : '' ) . '" href="' . $prev_url . '" title="Previous Episode">Prev</a>';
				$nextBtn = '<a class="button' . ( $next_url == '#' ? ' disabled' : '' ) . '" href="' . $next_url . '" title="Next Episode">Next</a>';



				$mp3_player = '';
				if ( $mp3_url = unity3_audio_transcription_src_url( $post->ID ) ) {

					$attr = array(
						'src' => $mp3_url,
						'loop' => '',
						'autoplay' => '',
						'preload' => 'auto'
					);

					$mp3_player =
						'<div class="audio-wrapper">' .
							 wp_audio_shortcode( $attr ) .
						'</div>';
				} else {
					$mp3_player = '<div class="audio-wrapper">No audio exists for this episode</div>';
				}

				$time = microtime(true) . '<br>';
				$hidden = '<input type="hidden" name="aht_post_id" value="' . $post->ID . '" />';

				$header_html = "{$episode_title} {$nextBtn} {$prevBtn} {$mp3_player} {$hidden}";
			}

		} else {
			$post = null;
		}

		wp_reset_postdata();
		if ( ! $post ) {
			return;
		}

		return $header_html;
	}

	function prepare_field( $field ) {

		global $post;
		if ( ! $post ) {
			return $field;
		}

		$status = get_post_meta( $post->ID, Unity3_Audio_Transcription::PM_STATUS, true );


		if ( self::KEY_NO_JOB === $field['key'] ) {

			if ( Unity3_Audio_Transcription::STATUS_COMPLETED === $status ) {
				return false; //dont need this when transcription is complete
			} else {
				$field['message'] = 'No transcription currently exists for this episode.';
			}

		} elseif ( self::KEY_VISIBILITY === $field['key'] ||
		           self::KEY_CONTENT === $field['key'] ) {

			if ( Unity3_Audio_Transcription::STATUS_COMPLETED !== $status ) {
				return false;
			}

		}

		return $field;
	}



	function process_value( $value, $key, $updating = false ) {
		$aht_post_id = intval( isset( $_REQUEST['aht_post_id'] ) ? $_REQUEST['aht_post_id'] : 0 );

		if ( $aht_post_id ) {
			if ( $updating ) {
				update_post_meta( $aht_post_id, $key, $value );
				$value = '';
			} else {
				$value = get_post_meta( $aht_post_id, $key, true );
			}

		}

		return $value;
	}

	function override_load( $value, $post_id, $field ) {
		if ( self::KEY_VISIBILITY === $field['key'] ||
		     self::KEY_CONTENT === $field['key'] ) {

			return $this->process_value( $value, $field['name'], false );

		}

		return $value;
	}

	function override_save( $value, $post_id, $field, $original ) {
		if ( self::KEY_VISIBILITY === $field['key'] ||
		     self::KEY_CONTENT === $field['key'] ) {

			return $this->process_value( $value, $field['name'], true );

		}

		return $value;
	}

}

new BF_Episode_Transcription_Editor();
