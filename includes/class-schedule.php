<?php
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Google_Maps;
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Geo_Loc_Data;

/**
 * Block template for posts
 * @link https://www.billerickson.net/gutenberg-block-templates/
 *
 */

class Blythe_Schedule {

	const CRON_HOOK = 'blythe_calendar_events_hook';
	const OP_SUMMARY_LAST_POST = 'blythe_search_summary_last_post_id';
	const OP_GEO_CACHE = 'blythe_search_geo_cache';

	const PM_EVENT_IDS_CACHE = 'blythe_event_ids_cache';

	public function __construct() {
		add_action( 'init', array(&$this, 'hide_template'), 999 );
		add_action( 'nf_save_sub', array(&$this, 'new_submission'), 10, 1 );

		add_action( 'blythe_plugin_deactivate', array(&$this, 'plugin_deactivate') );

		add_filter( 'wp_insert_post_data' , array(&$this, 'hide_data' ) , 999, 2 );
		add_filter( 'tribe_events_pre_get_posts', array(&$this, 'redirect_from_events') );
		add_filter( 'posts_where', array(&$this, 'restrict_events_where'), 100 );
		add_filter('acf/prepare_field/name=blythe_events_search_results', array(&$this, 'prepare_field'));
		add_filter( "get_post_metadata", array(&$this, 'nf_email_search_results'), 99, 3);


		//Cron
		add_action( self::CRON_HOOK, array(&$this, 'cron') );
		if ( !wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( strtotime('tomorrow +10 hours'), 'daily', self::CRON_HOOK );

			//initialize option so the cron will query the submissions after the last one on record (avoid a possible old backlog of submissions)
			$last_post_id = get_option( self::OP_SUMMARY_LAST_POST, 0);
			if ( intval($last_post_id) === 0 ) {
				$results = $this->get_submission_ids();
				$last_post_id = count( $results ) ? max($results) : 0;
				update_option( self::OP_SUMMARY_LAST_POST, $last_post_id );
			}
		}
	}


	function plugin_deactivate() {
		if ( $timestamp = wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}

	function cron() {

		$search_results_list = $this->get_recent_search_submissions( false );
		if ( count($search_results_list) ) {

			$email = get_option('admin_email');
			$title = 'Schedule Searches: ' . count($search_results_list);

			ob_start();
			include( BF::$dir . '/templates/email/schedule_search_admin_summary.php' );
			$body = ob_get_clean();

			$content_type = function() { return 'text/html'; };
			add_filter( 'wp_mail_content_type', $content_type );
			wp_mail( 'mail@blythefamily.com', $title, $body );
			remove_filter( 'wp_mail_content_type', $content_type );
		}


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

	private function get_recent_search_submissions( $only_with_events ) {

		//get the post id that serves as an offset for our pending query
		$results = $this->get_submission_ids( get_option( self::OP_SUMMARY_LAST_POST, true ) );

		if ( count( $results ) ) {
			update_option( self::OP_SUMMARY_LAST_POST, max( $results ) );
		}

		//now build the recent search submission list
		$search_results_list = [];
		foreach ( $results as $post_id ) {
			$item = $this->search_by_submission( $post_id );
			if ( is_array($item) && ( !$only_with_events || count( $item['events'] ) ) ) {
				$search_results_list[] = $item;
			}
		}

		return $search_results_list;
	}

	private function get_submission_ids( $greater_than = 0 ) {
		global $wpdb;

		$nf3_form_meta_table = $wpdb->prefix . 'nf3_form_meta';
		$greater_than = intval( $greater_than );

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT  p.ID
			    FROM  {$wpdb->posts} p 
			    INNER JOIN {$wpdb->postmeta} pm ON (
			        pm.post_id = p.ID AND 
                    pm.meta_key = '_form_id' AND 
                    pm.meta_value = ( 
                        SELECT MAX(`parent_id`) FROM {$nf3_form_meta_table} 
                        WHERE `key` = 'key' AND `value` = 'blythe_schedule_search_form'
                        LIMIT 1
                    )
                )
				WHERE p.ID > %d AND p.post_type = 'nf_sub' AND p.post_status = 'publish'"
			, $greater_than)
		);
	}

	private function search_by_submission( $submission_id ) {

		global $blythe_nf_submission_id;
		$submission_id = $blythe_nf_submission_id = intval( $submission_id );

		$sub = Ninja_Forms()->form()->get_sub( $submission_id );
		if ( !$sub ) {
			return new \WP_Error('Submission ID is not valid!');
		}

		$search_address =
			trim($sub->get_field_value( 'city' ) ) . ', ' .
            trim( $sub->get_field_value( 'us_state' ) ). ', ' .
            trim( $sub->get_field_value( 'zip' ) );


		$results = [
			'sub'     =>  $sub,
			'name'    => $sub->get_field_value( 'contact_name' ),
			'email'   => $sub->get_field_value( 'email' ),
			'address' => $search_address,
			'radius'  => intval( $sub->get_field_value( 'search_radius' ) ),
			'edit_link' => get_admin_url(null, "post.php?post={$submission_id}&action=edit" ),
			'events'  => false,
		];


		//*********************************************
		// SUBMISSION EVENTS CACHE
		$event_ids_cache = get_post_meta( $submission_id, self::PM_EVENT_IDS_CACHE, true );
		if ( is_array( $event_ids_cache ) ) {
			//return the list of events that the user would have seen
			$results['events'] = count( $event_ids_cache ) == 0 ? array() :
				tribe_get_events(array(
					'posts_per_page' => -1,
					'post__in' => $event_ids_cache,
					'tribe_geoloc'   => false //don't need geo
				));
			return $results;
		}
		//*********************************************


		//****************************************************
		// GEO CACHE
		$geo_loc_data = null;
		$cache_geo_data = get_option( self::OP_GEO_CACHE );
		if ( !is_array( $cache_geo_data) ) {
			$cache_geo_data = array();
		}

		$key = strtolower( trim( $search_address) );
		if ( isset($cache_geo_data[ $key ]) ) {
			$geo_loc_data = $cache_geo_data[ $key ];
		} else {
			//no cache item found so let's go talk with Google_Maps
			$maps = new Google_Maps();
			$google_data = $maps->resolve_to_coords( $search_address );

			if ( $google_data instanceof Geo_Loc_Data ) {

				$geo_loc_data = [
					'lat' => $google_data->get_lat(),
					'lng' => $google_data->get_lng(),
				];

				$cache_geo_data[ $key ] = $geo_loc_data;
				update_option( self::OP_GEO_CACHE, $cache_geo_data, false );
			} elseif ( is_wp_error( $google_data ) ) {
				//Error
				do_action( 'tribe_log', 'error', 'Geocoding_Handler', [
					'action' => 'geocode_resolution_failure',
					'code'    => $google_data->get_error_code(),
					'message' => $google_data->get_error_message(),
					'data'    => $google_data->get_error_data(),
				] );

				return $google_data; //error
			}
		}
		//*******************************************************

		if ( $geo_loc_data ) {
			add_filter( 'tribe_geoloc_geofence', array(&$this, 'geo_fence'), 99 );

			$results['events'] = tribe_get_events(array(
				'posts_per_page' => -1,
				'ends_after' => 'now',

				'tribe_geoloc'   => true,
				'tribe_geoloc_lat' => $geo_loc_data['lat'],
				'tribe_geoloc_lng' => $geo_loc_data['lng'],
			));

			remove_filter( 'tribe_geoloc_geofence', array(&$this, 'geo_fence'), 99 );

			//store the located events in a cache for future reference
			update_post_meta( $submission_id, self::PM_EVENT_IDS_CACHE, wp_list_pluck( $results['events'], 'ID' ) );
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








