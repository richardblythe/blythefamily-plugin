<?php
	$start_date = get_post_meta($event->ID, '_EventStartDate', true);
	$end_date = get_post_meta($event->ID, '_EventEndDate', true);

	$venue_id = get_post_meta($event->ID, '_EventVenueID', true);
	$venue = get_post($venue_id);

	$event_html = [
		'title'      => esc_html($venue->post_title),
		'date_range' => esc_html( date("F j, Y", strtotime($start_date)) . ' - ' . date("F j, Y", strtotime($end_date)) ),
		'location'   => esc_html(
			get_post_meta($venue_id, '_VenueAddress', true) . ', ' .
			get_post_meta($venue_id, '_VenueCity', true) . ', ' .
			get_post_meta($venue_id, '_VenueState', true) . ', ' .
			get_post_meta($venue_id, '_VenueZip', true)
		),

	];
?>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
	<tbody><tr>
		<td id="m_-7437770077905669010bodyText-8" style="font-family:'Open Sans',Arial,Helvetica,sans-serif;font-size:14px;line-height:150%;color:#6f6f6f">
			<h3 style="text-align:center;margin:0px">
				<?php echo $event_html['title']; ?>
			</h3><span class="im">
            <p style="margin-top:0px;margin-bottom:10px;line-height:150%;text-align:center;margin:0px">
	            <?php echo $event_html['date_range'] ?>
            </p>
            <p style="margin-top:0px;margin-bottom:10px;line-height:150%;text-align:center">
	            <?php echo $event_html['location']; ?>
            </p>
            <p style="margin-top:0px;margin-bottom:0px;line-height:150%"></p>
          </span>
		</td>
	</tr>
	</tbody>
</table>