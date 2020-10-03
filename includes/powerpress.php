<?php
function plt_hide_powerpress_metaboxes() {
    $screen = get_current_screen();
    if ( !$screen ) {
        return;
    }

    //Hide the "Podcast Episode" meta box.
    remove_meta_box('powerpress-podcast', $screen->id, 'normal');
}

add_action('add_meta_boxes', 'plt_hide_powerpress_metaboxes', 20);