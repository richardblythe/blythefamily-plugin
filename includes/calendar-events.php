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

add_filter( 'posts_where', 'restrict_events', 100 );
function restrict_events( $where_sql ) {
    global $wpdb;
    if ( is_admin() || ! class_exists( 'Tribe__Events__Main' ) ) {
        return $where_sql;
    }
    return $wpdb->prepare( " $where_sql AND $wpdb->posts.post_type <> %s ", Tribe__Events__Main::POSTTYPE );
}

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
			return 'Error in retrieving results:\r\n' . $geo_loc_data->get_error_message();

	} else {

		add_filter( 'tribe_geoloc_geofence', 'blythe_events_search_geo_fence', 99 );

		$events = tribe_get_events(array(
			'posts_per_page' => -1,
			'start_date'     => 'now',

			'tribe_geoloc'   => true,
			'tribe_geoloc_lat' => $geo_loc_data->get_lat(),
			'tribe_geoloc_lng' => $geo_loc_data->get_lng()
		));

		remove_filter( 'tribe_geoloc_geofence', 'blythe_events_search_geo_fence', 99 );


		if ($raw) {
			return $events;
		}

		$html_output = '';
		if ( count($events) ) {

			$result_label = count($events) . ' location' . (1==count($events) ? '' : 's');

			$html_output = "Thank you for inquiring about our upcoming schedule! We found <strong>{$result_label}</strong> in your search area.<br/><br/> ";

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
				'<strong>Notice: Any event shown above could change unexpectedly, so please contact us to confirm an event before making plans to attend.</strong><br/><br/>' .
				'God bless you!<br/>' .
				'The Blythe Family';
		} else {
			$html_output = "Thank you for inquiring about our upcoming schedule! Unfortunately, no dates were found in your specified search area.<br/><br/> " .
			          'God bless you!<br/>' .
			          'The Blythe Family';
		}

		return $html_output;
	}
}

//function blythe_nf_submission_search_admin_msg_prepare_field( $field ) {
//	$post = get_post();//submission post
//	$sub = Ninja_Forms()->form()->get_sub( $post->ID);
//	$email =  $sub->get_field_value( 'email' );
//	$field['message'] = "Below are the events that were found in the user\'s specified search area. " .
//	                  "You can copy/paste this information into a return email back to the user:<br\/>" .
//	                  "<a href=\"mailto:{$email}\">Email: {$email}</a>";
//	return $field;
//}
//add_filter('acf/prepare_field/key=field_6067724760d5b', 'blythe_nf_submission_search_admin_msg_prepare_field');

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
		$value = blythe_events_search_by_submission( $blythe_nf_submission_id );
	}
	return $value;
}
add_filter( "get_post_metadata", 'blythe_nf_email_search_results', 99, 3);

function blythe_new_submission($id) {
	global $blythe_nf_submission_id;
	$blythe_nf_submission_id = $id;
}
add_action( 'nf_create_sub', 'blythe_new_submission', 99, 1 );

function blythe_events_search_geo_fence( $default_geo_fence ) {
	global $blythe_nf_submission_id;
	$sub = Ninja_Forms()->form()->get_sub( $blythe_nf_submission_id );

	if ( $geofence = $sub->get_field_value( 'search_radius' ) ) {
		$default_geo_fence = $geofence;
	}

	return $default_geo_fence;
}