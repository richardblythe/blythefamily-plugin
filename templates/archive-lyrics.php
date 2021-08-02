<?php
//
///**
// * Archive Post Class
// *
// * Breaks the posts into three columns
// * @link http://www.billerickson.net/code/grid-loop-using-post-class
// *
// * @param array $classes
// * @return array
// */
//function be_archive_post_class( $classes ) {
//
//    // Don't run on single posts or pages
//    if( is_singular() )
//        return $classes;
//
//    $classes[] = 'one-half';
//    global $wp_query;
//    if( 0 == $wp_query->current_post || 0 == $wp_query->current_post % 3 )
//        $classes[] = 'first';
//    return $classes;
//}
//add_filter( 'post_class', 'be_archive_post_class' );
//
//genesis();