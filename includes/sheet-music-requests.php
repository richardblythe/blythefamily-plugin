<?php


function blythe_sheet_music_menu() {
	add_submenu_page( 'ninja-forms', 'Sheet Music', 'Sheet Music', 'manage_options', 'sheet-music-submissions', 'blythe_sheet_music_submissions' );
}
add_action( 'admin_menu', 'blythe_sheet_music_menu' );

function blythe_sheet_music_submissions() {

	global $wpdb;
	$nf_meta_field = '_field_11';
	$submissions = $wpdb->get_results("
	  select p.ID as ID, pm.meta_value as song_title
	       , count(*) as submission_count
	    from {$wpdb->posts} p
	         join {$wpdb->postmeta} pm on p.ID = pm.post_id
	   where p.post_type = 'nf_sub'
	     and p.post_status = 'publish'
	     and pm.meta_key = '{$nf_meta_field}'
	group by pm.meta_value
	order by submission_count DESC, pm.meta_value ASC" );

	//*******  Layout The Page  ***********
	?>

	<div class="wrap">
		<h2>Sheet Music Submissions</h2>
	</div>

	<ul>
		<?php foreach($submissions as $song) { ?>
			<li class="song">
				<a href="<?php echo get_edit_post_link($song->ID); ?>">
					<?php echo $song->song_title; ?>: <?php echo $song->submission_count; ?>
				</a>
			</li>
		<?php } ?>
	</ul>
	<?php
}

