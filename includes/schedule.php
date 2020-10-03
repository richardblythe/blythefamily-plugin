<?php
//******************************************************************************
//*********               Blythe Schedule

function blythe_schedule_init() {
    if (isset($_REQUEST['blythe-locations-clear-cache'])) {
	    //the button above is only clicked in a submission page.
        //so the ID below will reference the correct submission
        blythe_schedule_search_clear_cache( get_the_ID() );
    } elseif (isset($_REQUEST['blythe-locations-send-email'])) {
	    $id = $_POST['post_ID'];

        $sub = Ninja_Forms()->form()->get_sub( $id );
	    $field = get_field_object('email_body', $id);



	    $subject = 'Blythe Family - Schedule Inquiry';
	    $to = $sub->get_field_value('email', $id );
	    $body = $_POST['acf'][$field['key']];
	    $headers = array('Content-Type: text/html; charset=UTF-8');

	    wp_mail( $to, $subject, $body, $headers );
    }
}
add_action('wp_loaded', 'blythe_schedule_init');

if (is_callable('unity_register_post_type')) {
	unity3_register_post_type(
		'blythe_event',
		'Event',
		'Events',
		array(
			'public' => true,
			'publicly_queryable' => false,
		),
		false
	);
}

add_action( 'pre_get_posts', 'blythe_event_default_admin_sort' );

function blythe_event_default_admin_sort( $query ) {
	if( is_admin() && 'blythe_event' == $query->get('post_type') ) {
		$query->set('meta_key','start_date');
		$query->set('orderby','meta_value_num');
		$query->set('order','asc');
    }
}

function blythe_schedule_search_clear_cache( $submission_id ) {
	$transient_key = "blythe_schedule_search_{$submission_id}";
    delete_transient($transient_key);
}


function blythe_schedule_submission_query( $submission_id ) {

    //$address, $radius, $include_state_regions
	$post = get_post($submission_id);
	if (!($post instanceof WP_Post)) return null;
	$id = $post->ID;
    $today = date('Ymd');


	//if we've specified any state regions, it overrides the radius data
    $state_regions = get_field('state_regions', $id  );
    if ( 0 !== count($state_regions) ) {
	    //go with a state(s) region query

        return new WP_Query( array(
		    'post_type' => 'blythe_event',
		    'posts_per_page' => -1,
		    'meta_query' => array(
			    'state_regions_clause' => array(
				    'key' => 'state',
				    'value' => $state_regions,
				    'compare' => 'IN'
			    ),
                'start_date_clause' => array(
	                'key' => 'start_date',
	                'value' => $today,
	                'compare' => '>='
                )
		    ),
		    'orderby' => array('start_date_clause' => 'ASC')
	    ));
    } else {
        //go with a radius query
	    $sub = Ninja_Forms()->form()->get_sub( $id );
	    $address = urlencode(
		    ($sub->get_field_value('city') . ',' .
		     $sub->get_field_value('state') . ',' .
		     $sub->get_field_value('zip')
		    ));
	    $radius = $sub->get_field_value('search_radius');


	    //Get the LAT/LNG of the requested centering address
	    $apiKey = 'AIzaSyDqz4oENh1FS3QmNEWT3fIopfwURLY0kOw';
	    $googleMapUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";
	    $geocodeResponseData = file_get_contents($googleMapUrl);
	    $responseData = json_decode($geocodeResponseData, true);
	    if($responseData['status'] == 'OK') {

		    $lat         = isset( $responseData['results'][0]['geometry']['location']['lat'] ) ? $responseData['results'][0]['geometry']['location']['lat'] : "";
		    $lng        = isset( $responseData['results'][0]['geometry']['location']['lng'] ) ? $responseData['results'][0]['geometry']['location']['lng'] : "";

		    //Now get potential locations within the specified radius of this lng/lat
            // Declare the query arguments
            $args = array(
                'post_type' => 'blythe_event',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    'lat_clause' => array(
                        'key'     => 'lat',
                        'compare' => 'EXISTS',
                    ),
                    'lng_clause' => array(
                        'key'     => 'lng',
                        'compare' => 'EXISTS',
                    ),
                    'start_date_clause' => array(
	                    'key' => 'start_date',
	                    'value' => $today,
	                    'compare' => '>='
                    )
                ),
                'orderby' => array('start_date_clause' => 'ASC')
            );

            global $blythe_event_query_data;
            $blythe_event_query_data = array(
                'lat' => $lat,
                'lng' => $lng,
                'rad' => $radius
            );

            // Add our filter before executing the query
            add_filter( 'posts_where' , '_radius_query_where' );

            // Execute the query
            $radius_query = new WP_Query( $args );

            // Remove the filter just to be sure its
            // not used again by non-related queries
            remove_filter( 'posts_where' , '_radius_query_where' );
            $blythe_event_query = null;

            return $radius_query;
	    } else {
		    return new WP_Error($responseData['status'],'Error with Google Geocode request', $responseData);
	    }
    }

    return null; //should not reach this point
}

function _radius_query_where( $where )
{
    global $blythe_event_query_data;

	// Append our radius calculation to the WHERE
	$where .= " AND ( 
         ( 3959 * acos( cos( radians(" .  $blythe_event_query_data['lat'] . ") ) 
                        * cos( radians( mt1.meta_value ) ) 
                        * cos( radians( mt2.meta_value ) 
                        - radians(" .  $blythe_event_query_data['lng'] . ") ) 
                        + sin( radians(" .  $blythe_event_query_data['lat'] . ") ) 
                        * sin( radians( mt1.meta_value ) ) ) ) <= " .  $blythe_event_query_data['rad'] . ")";

	// Return the updated WHERE part of the query
	return $where;
}


function blythe_schedule_submission_email_body() {
	$query = blythe_schedule_submission_query( get_the_ID() );

	ob_start();

	echo 'Thank you for contact us regarding our upcoming schedule.';
	$id = -1;

	if ($query instanceof WP_Error) {
		echo 'There was an error processing the data:<br>' . $query->get_error_message();
    } else if ( $query->have_posts() ) {
		echo ' Below are the location(s) that were found in your specified area.<br><strong>Any of these dates could change for various reasons, so please contact us before making the drive.</strong><hr>';
		while ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
			$start_date = date("M-d-Y", strtotime(get_post_meta( $id,'start_date', true)));
			$end_date = date("M-d-Y", strtotime(get_post_meta( $id,'end_date', true)));
			?>
            <strong><?php the_title(); ?></strong>
			<?php echo "{$start_date} - {$end_date}"; ?>
            <br>
			<?php echo
				get_post_meta( $id,'address', true) . ', ' .
				get_post_meta( $id,'city', true) .    ', ' .
				get_post_meta( $id,'state', true) .   ', ' .
				get_post_meta( $id,'zip', true); ?>
            <br>
            <br>
			<?php
		}

		/* Restore original Post Data
		 * NB: Because we are using new WP_Query we aren't stomping on the
		 * original $wp_query and it does not need to be reset with
		 * wp_reset_query(). We just need to set the post data back up with
		 * wp_reset_postdata().
		 */
		wp_reset_postdata();
	} else {
		echo "<strong>Unfortunately, no locations were found in the requested area.</strong>";
	}

	echo "<hr>Please feel free to contact us anytime with any questions or comments.";
	echo " God bless you!<br>The Blythe Family";

	return ob_get_clean();
}

add_filter('acf/load_value/name=email_body', 'blythe_schedule_submission_email_body');

function blythe_schedule_submission_email_body_after( $fields, $post_id ) {
    if (1 == count($fields) && 'email_body' == $fields[0]['name']) {
	    echo '<input id="blythe-locations-send-email" name="blythe-locations-send-email" type="submit" class="button button-primary button-large" style="margin: 20px;" value="Send Email">';
    }
}
add_action( 'acf/render_fields', 'blythe_schedule_submission_email_body_after', 10, 2 );

// the custom field is dynamically populated so we don't
// want to clutter up the database with saving it's data.
function my_acf_prevent_email_body_save() {
    return false;
}
add_filter('acf/update_value/name=email_body', 'my_acf_prevent_email_body_save');