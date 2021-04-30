<?php
add_shortcode('blythe_download_only', function () {
	return '<p style="color: red;">This item is available via download only</p>';
});

add_shortcode('bf_album_tracks_product_overview', function () {
	return 'This collection of accompaniment tracks are available in the original recorded key(s) and feature:';
});

add_shortcode('bf_album_tracks_cd_desc', function () {
	return 'The entire collection of accompaniment tracks, made available on a custom compact disc.';
});

add_shortcode('bf_album_tracks_usb_desc', function () {
	return 'The entire collection of accompaniment tracks, made available on a custom USB flash drive.';
});

add_shortcode('bf_album_tracks_dl_all_desc', function () {
	return 'The entire collection of accompaniment tracks, made available as a digital download';
});

add_shortcode('bf_album_tracks_dl_single_desc', function () {
	return 'Available as a digital download.';
});