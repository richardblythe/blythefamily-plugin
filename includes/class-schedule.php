<?php
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Google_Maps as GoogleMaps;
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Geo_Loc_Data as GeoData;

/**
 * Block template for posts
 * @link https://www.billerickson.net/gutenberg-block-templates/
 *
 */

class Blythe_Schedule {

	const CRON_HOOK = 'blythe_calendar_events_hook';
	const OP_LAST_DATE = 'blythe_email_last_queried_date';

	public function __construct() {
		add_action( 'init', array(&$this, 'hide_template'), 999 );
		add_action( 'nf_save_sub', array(&$this, 'new_submission'), 10, 1 );

		add_filter( 'wp_insert_post_data' , array(&$this, 'hide_data' ) , 999, 2 );
		add_filter( 'tribe_events_pre_get_posts', array(&$this, 'redirect_from_events') );
		add_filter( 'posts_where', array(&$this, 'restrict_events_where'), 100 );
		add_filter('acf/prepare_field/name=blythe_events_search_results', array(&$this, 'prepare_field'));
		add_filter( "get_post_metadata", array(&$this, 'nf_email_search_results'), 99, 3);


		//TODO  Finish Cron Code
		//Cron
		//add_action( self::CRON_HOOK, array(&$this, 'cron') );

//		if ( !wp_next_scheduled( self::CRON_HOOK ) ) {
//			wp_schedule_event( strtotime('tomorrow +10 hours'), 'daily', self::CRON_HOOK );
//		}
	}

	function cron() {


		$last_queried_date = get_option( self::OP_LAST_DATE, time());

		$posts = get_posts([
			'post_type' => 'nf_sub',
			'numberposts' => -1,
			'date_query' => array(
				array(
					'after'     => $last_queried_date,
				),
			),
		]);

		update_option( self::OP_LAST_DATE, time() );

		if ( !count($posts) ) {
			$this->stop_cron();
			return;
		}


		$body_content = '';
		foreach ($posts as $post ) {
			$events = $this->search_by_submission( $post->ID, true );
			foreach ( $events as $event ) {

				$start_date = get_post_meta($event->ID, '_EventStartDate', true);
				$end_date = get_post_meta($event->ID, '_EventEndDate', true);

				$venue_id = get_post_meta($event->ID, '_EventVenueID', true);
				$venue = get_post($venue_id);



				$body_content .=
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
		}


		//format an email to me
		$email = get_option('admin_email');
		$title = 'Schedule Searches: ' . count($posts);
		$body =
			'<!DOCTYPE html PUBLIC "...">
			<html xmlns="https://www.w3.org/1999/xhtml">
			<head>
			  <title>' . $title . '</title>
			<style></style>
			</head>
				<body>'.
					$body_content
				.'</body>
			</html>';

		$content_type = function() { return 'text/html'; };
		add_filter( 'wp_mail_content_type', $content_type );
		wp_mail( $email, $title, $body );
		remove_filter( 'wp_mail_content_type', $content_type );

	}

	function stop_cron() {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		wp_unschedule_event( $timestamp, self::CRON_HOOK );
	}


	public function hide_template() {
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

	public function hide_data( $data , $postarr ) {
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

	//restrict access on the front end
	public function redirect_from_events( $query ) {

		if ( ! $query->is_main_query() || ! $query->get( 'eventDisplay' ) )
			return;

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

	public function restrict_events_where( $where_sql ) {

		global $wpdb;
		global $blythe_nf_submission_id;

		if ( is_admin() || $blythe_nf_submission_id || !class_exists( 'Tribe__Events__Main' ) ) {
			return $where_sql;
		}
		return $wpdb->prepare( " $where_sql AND $wpdb->posts.post_type <> %s ", Tribe__Events__Main::POSTTYPE );
	}


	public function new_submission( $id ) {
		global $blythe_nf_submission_id;
		$blythe_nf_submission_id = $id;
	}


	public function prepare_field( $field ) {
		//used in the acf meta box
		$post = get_post();
		global $blythe_nf_submission_id;
		$blythe_nf_submission_id = $post->ID;

		$search_results = $this->search_by_submission( $blythe_nf_submission_id );
		$email_template = null;

		if ( $search_results instanceof \WP_Error ) {
			$error = $search_results;
			$field['value'] = $error->get_error_message();
		} elseif ( $search_results['events'] ) {
			$email_template = BF::$dir . '/templates/email/part/event_listing.php';
			ob_start();
			foreach ( $search_results['events'] as $event) {
				include( $email_template );
			}
			$field['value'] = ob_get_clean();
		} else {
			$field['value'] = 'No results';
		}

		return $field;
	}

	public function nf_email_search_results($value, $post_id, $meta_key) {
		//Using a custom/dynamic postmeta field in the Admin email to attach search results
		if ( 'blythe_nf_email_search_results' === $meta_key ) {
			global $blythe_nf_submission_id;

			$search_results = $this->search_by_submission( $blythe_nf_submission_id );
			$email_template = null;
			if ( $search_results && !$search_results instanceof \WP_Error && $search_results['events'] ) {
				$email_template = BF::$dir . '/templates/email/schedule_search_results.php';
			} else {
				$email_template = BF::$dir . '/templates/email/schedule_search_no_results.php';
			}

			ob_start();
			include( $email_template );
			$value = ob_get_clean();
		}
		return $value;
	}



	private function search_by_submission( $submission_id ) {

		$sub = Ninja_Forms()->form()->get_sub( $submission_id );
		if ( !$sub ) {
			return new \WP_Error('Submission ID is not valid!');
		}

		$search_address =
			$sub->get_field_value( 'city' ) . ', ' .
            $sub->get_field_value( 'us_state' ) . ', ' .
            $sub->get_field_value( 'zip' );

		$results = [
			'sub'           =>  $sub,
			'address' => $search_address,
			'radius'         => intval( $sub->get_field_value( 'search_radius' ) ),
			'events' => false,
		];


		$maps = new GoogleMaps();
		$geo_loc_data = $maps->resolve_to_coords( $search_address );
		/** @var \WP_Error $geo_loc_data */
		if ( $geo_loc_data instanceof \WP_Error ) {
			do_action( 'tribe_log', 'error', 'Geocoding_Handler', [
				'action' => 'geofcode_resolution_failure',
				'code'    => $geo_loc_data->get_error_code(),
				'message' => $geo_loc_data->get_error_message(),
				'data'    => $geo_loc_data->get_error_data(),
			] );

			return $geo_loc_data; //error

		} else {

			add_filter( 'tribe_geoloc_geofence', array(&$this, 'geo_fence'), 99 );

			$results['events'] = tribe_get_events(array(
				'posts_per_page' => -1,
				'start_date'     => 'now',

				'tribe_geoloc'   => true,
				'tribe_geoloc_lat' => $geo_loc_data->get_lat(),
				'tribe_geoloc_lng' => $geo_loc_data->get_lng(),
			));

			remove_filter( 'tribe_geoloc_geofence', array(&$this, 'geo_fence'), 99 );
		}

		return $results;
	}

	public function geo_fence( $default_geo_fence ) {
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
}

new Blythe_Schedule();








