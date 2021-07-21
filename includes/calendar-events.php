<?php
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Google_Maps as GoogleMaps;
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Geo_Loc_Data as GeoData;

/**
 * Block template for posts
 * @link https://www.billerickson.net/gutenberg-block-templates/
 *
 */
function blythe_events_calendar_block_template() {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$post_type_object = get_post_type_object( Tribe__Events__Main::POSTTYPE );
		$post_type_object->template = array(
			array( 'tribe/event-datetime' ),
			array( 'tribe/event-venue' ),
			array( 'tribe/event-links' ),
		);
		$post_type_object->publicly_queryable = false;

		//tribe_venue
		if ( $post_type_object = get_post_type_object( 'tribe_venue' ) ) {
			$post_type_object->publicly_queryable = false;
		}

		//tribe_organizer
		if ( $post_type_object = get_post_type_object( 'tribe_organizer' ) ) {
			$post_type_object->publicly_queryable = false;
		}
	}
}
add_action( 'init', 'blythe_events_calendar_block_template', 999 );



function filter_post_data( $data , $postarr ) {
	// Change post title
	if ( empty($data['post_title']) && class_exists( 'Tribe__Events__Main' ) && $postarr['post_type'] === Tribe__Events__Main::POSTTYPE ) {
		//Overwrite event title with venue name and the year of the event
		$venue_id = get_post_meta($postarr['ID'],'_EventVenueID',true);
		if ( $venue = get_post( $venue_id ) ) {
			$start_date = null;
			if ( !$start_date = get_post_meta($postarr['ID'],'_EventStartDate',true) ) {
				$start_date = 'now';
			}
			$data['post_title'] = $venue->post_title . ' - ' . date("Y", strtotime($start_date));
			$data['post_name'] = sanitize_title($data['post_title']);
		}
	}



	return $data;
}
add_filter( 'wp_insert_post_data' , 'filter_post_data' , '99', 2 );


//restrict access on the front end
function blythe_events_calendar_redirect_from_events( $query ) {

	if ( ! $query->is_main_query() || ! $query->get( 'eventDisplay' ) )
		return;

	// Look for a page with a slug of "logged-in-users-only".
//        $target_page = get_posts( [
//            'post_type' => 'page',
//            'name' => 'logged-in-users-only'
//        ] );

	$target_page = '';

	// Use the target page URL if found, else use the home page URL.
	if ( empty( $target_page ) ) {
		$url = get_home_url();
	} else {
		$target_page = current( $target_page );
		$url = get_permalink( $target_page->ID );
	}

	// Redirect!
	wp_safe_redirect( $url );
	exit;
}
add_filter( 'tribe_events_pre_get_posts', 'blythe_events_calendar_redirect_from_events' );


function blythe_restrict_events( $where_sql ) {

	global $wpdb;
	global $blythe_nf_submission_id;

	if ( is_admin() || $blythe_nf_submission_id || !class_exists( 'Tribe__Events__Main' ) ) {
		return $where_sql;
	}
	return $wpdb->prepare( " $where_sql AND $wpdb->posts.post_type <> %s ", Tribe__Events__Main::POSTTYPE );
}
add_filter( 'posts_where', 'blythe_restrict_events', 100 );

function blythe_events_search_by_submission( $submission_id, $raw = false ) {
	$sub = Ninja_Forms()->form()->get_sub( $submission_id );

	$address =  $sub->get_field_value( 'city' ) . ', ' .
	            $sub->get_field_value( 'us_state' ) . ', ' .
	            $sub->get_field_value( 'zip' );


	$maps = new GoogleMaps();
	$geo_loc_data = $maps->resolve_to_coords( $address );
	/** @var \WP_Error $geo_loc_data */
	if ( $geo_loc_data instanceof \WP_Error ) {
		do_action( 'tribe_log', 'error', 'Geocoding_Handler', [
			'action' => 'geofcode_resolution_failure',
			'code'    => $geo_loc_data->get_error_code(),
			'message' => $geo_loc_data->get_error_message(),
			'data'    => $geo_loc_data->get_error_data(),
		] );

		if ($raw)
			return $geo_loc_data;
		else
			return 'Error in retrieving results.  Address: ' . $address  . '. Msg: ' . $geo_loc_data->get_error_message();

	} else {

		add_filter( 'tribe_geoloc_geofence', 'blythe_events_search_geo_fence', 99 );

		$events = tribe_get_events(array(
			'posts_per_page' => -1,
			'start_date'     => 'now',

			'tribe_geoloc'   => true,
			'tribe_geoloc_lat' => $geo_loc_data->get_lat(),
			'tribe_geoloc_lng' => $geo_loc_data->get_lng(),
		));

		remove_filter( 'tribe_geoloc_geofence', 'blythe_events_search_geo_fence', 99 );


		if ($raw) {
			return $events;
		}

		////DEBUG TRACING
		// update_option( 'func_blythe_events_search_by_submission',
		//     ('id: ' . $submission_id .
		//     ', address: ' . $address .
		//     ', geo: [' . ($geo_loc_data->get_lat() . ', ' . $geo_loc_data->get_lng() ) . ']' .
		//     ', events: ' . count($events) ) );

		$html_output = '';
		if ( count($events) ) {

			$html_output = "Thank you for your schedule inquiry! Listed below are the upcoming event(s) in your search area:<br/><br/> ";

			foreach ( $events as $event ) {

				$start_date = get_post_meta($event->ID, '_EventStartDate', true);
				$end_date = get_post_meta($event->ID, '_EventEndDate', true);

				$venue_id = get_post_meta($event->ID, '_EventVenueID', true);
				$venue = get_post($venue_id);



				$html_output .=
					//Location Title
					'<strong>' . $venue->post_title . '</strong><br/>' .
					//Date of the event
					date("F j, Y", strtotime($start_date)) . ' - ' . date("F j, Y", strtotime($end_date)) . '<br/>' .
					//Location Address
					get_post_meta($venue_id, '_VenueAddress', true) . ', ' .
					get_post_meta($venue_id, '_VenueCity', true) . ', ' .
					get_post_meta($venue_id, '_VenueState', true) . ', ' .
					get_post_meta($venue_id, '_VenueZip', true) . '<br/><br/><br/>';
			}
			$html_output .=
				'<strong>Notice: Any event(s) shown above are subject to change without notice. Please contact us to confirm an event before making plans to attend.</strong>';
		} else {
			$html_output = "Thank you for your schedule inquiry! Unfortunately, no dates were found in your specified search area.<br/><br/> " .
			               'God bless you!<br/>' .
			               'The Blythe Family';
		}

		return $html_output;
	}
}


////DEBUG TRACING
// function blythe_tribe_get_events( $events, $args, $full ) {

//     update_option( 'func_blythe_tribe_get_events', $args );
//     return $events;

// }
// add_filter( 'tribe_get_events', 'blythe_tribe_get_events', 99, 3);

// function blythe_log_events_query( $cache_type,  $tribe_events_name,  $args ) {
//     update_option( 'func_blythe_log_events_query', array( $cache_type, $args ) );
// }
// add_action('log', 'blythe_log_events_query', 99, 3 );



// function blythe_dump_request( $input ) {

//     if ( strpos( $input, "g7Mz3ejc_posts.post_type = 'tribe_events'" ) ) {
//          update_option( 'func_blythe_dump_request', array( time(), $input ) );
//     }

//     return $input;
// }
// add_filter( 'posts_request', 'blythe_dump_request' );

function blythe_nf_submission_search_results_prepare_field( $field ) {
	$post = get_post();
	global $blythe_nf_submission_id;
	$blythe_nf_submission_id = $post->ID;
	$field['value'] = blythe_events_search_by_submission( $blythe_nf_submission_id ); //gets html output
	return $field;
}
add_filter('acf/prepare_field/name=blythe_events_search_results', 'blythe_nf_submission_search_results_prepare_field');


function blythe_nf_email_search_results($value, $post_id, $meta_key) {
	//Using a custom/dynamic postmeta field in the Admin email to attach search results
	if ( 'blythe_nf_email_search_results' === $meta_key ) {
		global $blythe_nf_submission_id;

		////DEBUG TRACING
		//update_option( 'func_blythe_nf_email_search_results', ('id: ' . $blythe_nf_submission_id . ', time: ' . time()) );

		$value = blythe_events_search_by_submission( $blythe_nf_submission_id );
	}
	return $value;
}
add_filter( "get_post_metadata", 'blythe_nf_email_search_results', 99, 3);

function blythe_new_submission( $id ) {
	global $blythe_nf_submission_id;
	$blythe_nf_submission_id = $id;

	////DEBUG TRACING
	//update_option( 'func_blythe_new_submission', ('id: ' . $blythe_nf_submission_id . ', time: ' . time() ) );
}
add_action( 'nf_save_sub', 'blythe_new_submission', 10, 1 );

function blythe_events_search_geo_fence( $default_geo_fence ) {
	global $blythe_nf_submission_id;

	$sub = Ninja_Forms()->form()->get_sub( $blythe_nf_submission_id );

	$geofence = intval( $sub->get_field_value( 'search_radius' ) );
	if ( $geofence ) {
		//reduce geofence equal or greater than 100 to prevent overage;
		$default_geo_fence = $geofence >= 100 ? ($geofence * .70) : $geofence;
	}

	////DEBUG TRACING
	//update_option( 'func_blythe_events_search_geo_fence', ( 'id: ' . $blythe_nf_submission_id . ', fence: ' . $default_geo_fence ) );

	return $default_geo_fence;
}