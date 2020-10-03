<?php

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

genesis();