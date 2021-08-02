<?php
//function plt_hide_powerpress_metaboxes() {
//    $screen = get_current_screen();
//    if ( !$screen ) {
//        return;
//    }
//
//    //Hide the "Podcast Episode" meta box.
//    remove_meta_box('powerpress-podcast', $screen->id, 'normal');
//}
//
//add_action('add_meta_boxes', 'plt_hide_powerpress_metaboxes', 20);


//function blythe_podcast_feed_clean_blocks( $block_content, $parsed_block ) {
//	//removes the weird double spacing issue in episode show notes
//	if ( empty( $parsed_block['blockName'] ) && "\n\n" === $block_content ) {
//		$block_content = '';
//	} elseif ( 'blythe/episode-info' === $parsed_block['blockName'] ) {
//		$matches = null;
//		preg_match('/<p class="description">(.*)<\/p><h3>(.*)<\/h3>.*<ul class="scriptures">(.*)<\/ul>/', $block_content, $matches);
//		if ( count($matches) == 4 ) {
//			$block_content =
//				( '<p>' . $matches[1] . '</p>' ) .
//				"\n" .
//				( '<h3>' . $matches[2] . '</h3>' ) .
//				( '<ul>' . $matches[3] . '</ul>' );
//		}
//	}
//
//	return $block_content;
//}


//add_filter('the_excerpt_rss', function () {
//	global $blythe_podcast_format_is_excerpt;
//	$blythe_podcast_format_is_excerpt = true;
//	$content = get_the_content_feed('rss2');
//	$blythe_podcast_format_is_excerpt = false;
//
//	return;
//}, 999, 1);

//add_action( "do_feed_podcast", function (){
//	add_filter( 'render_block', 'blythe_podcast_feed_clean_blocks', 999, 2 );
//}, 1);