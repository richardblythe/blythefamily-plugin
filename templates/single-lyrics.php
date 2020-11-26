<?php
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
        <h3>Resources</h3>


        <ul>
        <?php
        if ( $album_id = get_post_meta( get_the_ID(), 'album_id', true) ) {
            echo ( '<li><i class="fas fa-compact-disc"></i>' . sprintf('<a href="%s">', get_permalink( $album_id ) ) . ' View Project</a></li>' );
        }

        if ( $sound_track_id = get_post_meta( get_the_ID(), 'sound_track_id', true) ) {
            echo ( '<li><i class="fas fa-microphone-alt"></i>' . sprintf('<a href="%s">', get_permalink( $sound_track_id ) ) . ' Soundtrack Available!</a></li>' );
        }

        $sheet_music = get_post_meta( get_the_ID(), 'sheet_music', true);
        echo ( '<li><i class="fas fa-book-open"></i>' . sprintf('<a href="%s">', get_permalink( $sheet_music ) ) . ( $sheet_music ? ' Sheet Music Available!' : 'Request Sheet Music' ) . '</a></li>' );
        echo '</ul>';
        ?>


        <div class="additional-songs">
            <h4><i class="far fa-lightbulb"></i> You might also like:</h4>
            <a href="#">Come Forth As Gold</a>
        </div>
    </div>
<?php
    echo '</div>';


    return;

    if ( $album_id = get_post_meta( get_the_ID(), 'album_id', true) ) {

        echo '<div class="one-fourth">';
        echo '<h3>From The Album:</h3>';
        printf('<a href="%s">', get_permalink( $album_id ) );
        genesis_image( array( 'post_id' => $album_id, 'size' => 'medium' ) );
        echo '</a></div>';
    }

    if ( $sound_track_id = get_post_meta( get_the_ID(), 'sound_track_id', true) ) {

        echo '<div class="one-fourth">';
        echo '<h3>Sound Track Available!</h3>';
        printf('<a href="%s">', get_permalink( $sound_track_id ) );
        genesis_image( array( 'post_id' => $sound_track_id, 'size' => 'medium' ) );
        echo '</a></div>';
    }

    if ( $sheet_music = get_post_meta( get_the_ID(), 'sheet_music', true) ) {

        echo '<div class="one-fourth">';
        printf('<a href="%s">', get_permalink( $sheet_music ) );
        genesis_image( array( 'post_id' => $sheet_music, 'size' => 'medium' ) );
        echo '</a></div>';
    } else {
        echo '<a href="#">Request Sheet Music</a>';
    }

    echo '</div>'; //end .one-third
});

// /** Remove Post Info */
// remove_action('genesis_before_post_content','genesis_post_info');
// remove_action('genesis_after_post_content','genesis_post_meta');

genesis();

