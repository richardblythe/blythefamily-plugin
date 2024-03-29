f<?php
//add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar', 99 );
//
//function cd_change_genesis_sidebar() {
//    remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); //remove the default genesis sidebar
//    add_action( 'genesis_sidebar', 'cd_do_sidebar' ); //add an action hook to call the function for my custom sidebar
//}
//add_action('get_header','cd_change_genesis_sidebar');

remove_action( 'studio_hero_section', 'studio_page_title', 10 );

remove_action( 'genesis_entry_content', 'genesis_do_singular_image', 8 );
// Move Entry Header
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
//
add_action( 'genesis_entry_content', 'genesis_entry_header_markup_open', 2 );
add_action( 'genesis_entry_content', 'genesis_do_post_title',3 );
add_action( 'genesis_entry_content', 'genesis_post_info', 4 );
add_action( 'genesis_entry_content', 'genesis_entry_header_markup_close', 5 );




add_action( 'genesis_entry_content', function() {
    echo '<a href="#lyric-resources" class="lyrics-resource-jump">(Jump to lyric resources)</a>';
    echo '<p class="lyrics-print-only" style="display: none;">' . get_permalink() . '</p>';
}, 6 ); //right after genesis_entry_header_markup_close

add_action( 'genesis_entry_content', function (){
	$copyright  = get_post_meta( get_the_ID(), 'copyright', true);
	$ccli = get_post_meta( get_the_ID(), 'ccli_song', true);

	if ( $copyright || $ccli ) {

		echo '<div class="copyright-info">';

		echo $copyright ? ( '<span class="copyright">Copyright &copy; ' . $copyright . '</span>') : null;
		echo $ccli ? ( '<span class="ccli-song">CCLI Song# ' . $ccli . '</span>') : null;

		echo '</div>';
		echo '<p class="lyrics-print-only" style="display: none;">For more music visit: blythefamily.com</p>';
	}
}, 100 );

add_action( 'genesis_after_content', function() {

    echo '<div class="lyrics-info">';

    if ( $youtube_url = get_post_meta( get_the_ID(), 'youtube', true) ) {
        ?>
        <div class="youtube-container two-thirds first">
            <h3>Listen</h3>
            <?php echo unity3_embed_responsive( $youtube_url ); ?>
        </div>
        <?php
    }

    ?>

    <div class="resource-container<?php echo ( $youtube_url ? ' one-third' : ''); ?>">
        <h3 id="lyric-resources">Resources</h3>
        <ul>
        <?php
        //**************************
        // ALBUM
        if ( $album_id = get_post_meta( get_the_ID(), 'album_id', true) ) {
            echo ( '<li><i class="fas fa-compact-disc"></i>&nbsp;' . sprintf('<a href="%s">', get_permalink( $album_id ) ) . ' View Project</a></li>' );
        }

        //**************************
        // SOUND TRACK
        if ( $sound_track_id = get_post_meta( get_the_ID(), 'sound_track_id', true) ) {
            echo ( '<li><i class="fas fa-microphone-alt"></i>&nbsp;' . sprintf('<a href="%s">', get_permalink( $sound_track_id ) ) . ' Soundtrack Available!</a></li>' );
        }

        //only output the next two fields if we hold the copyright
        if ( $copyright  = get_post_meta( get_the_ID(), 'copyright', true) ) {
	        //**************************
	        // SHEET MUSIC
	        $permalink = '';
	        if ( $sheet_music = get_post_meta( get_the_ID(), 'sheet_music', true) ) {
		        $permalink = get_permalink( $sheet_music );
	        } else {
		        $page = get_page_by_path( 'sheet-music-request' );
		        $permalink = add_query_arg ('song_title', get_the_title(), get_permalink ( $page )) ;
	        }

	        echo ( '<li><i class="fas fa-book-open"></i>&nbsp;' . sprintf('<a href="%s">', $permalink ) . ( $sheet_music ? ' Sheet Music Available!' : 'Request Sheet Music' ) . '</a></li>' );



	        //**************************
	        // RECORDING LICENSE
            $permalink = 'https://publishing.blythefamily.com/';
	        echo ( '<li><i class="fas fa-file-contract"></i>&nbsp;' . sprintf('<a href="%s">', $permalink ) . ( 'Recording License' ) . '</a></li>' );
        }


        echo '</ul>';
        ?>

        <?php
        //----------------------------------------------
        //Get the list of related songs...
        $related_songs = get_post_meta( get_the_ID(), 'related_songs', true);
        ?>
        <?php if( $related_songs ): ?>
        <div class="additional-songs">
            <h4><i class="far fa-lightbulb"></i> You might also like:</h4>
            <ul>
		        <?php foreach( $related_songs as $song_id ): ?>
                    <li>
                        <a href="<?php echo get_permalink( $song_id ); ?>">
					        <?php echo get_the_title( $song_id ); ?>
                        </a>
                    </li>
		        <?php endforeach; ?>
            </ul>
	        <?php endif; ?>
        </div>
    </div>
</div>
<?php
});

genesis();

