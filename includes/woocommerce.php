<?php
//------------------------------------
//* Woo-Commerce Code
//------------------------------------

update_option( 'woocommerce_prepend_shop_page_to_urls', 'yes' );
add_theme_support( 'genesis-connect-woocommerce' );
add_filter( 'loop_shop_per_page', function() { return 6; }, 20 );

function blythe_woocart_add_class($classes) {
    $classes .= ' menu-item';
    return $classes;
}
add_filter( 'wpmenucart_menu_item_classes', 'blythe_woocart_add_class' );


/*
 * Remove the add to cart button at the bottom of an item
 */
function remove_add_to_cart_buttons() {
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
}
add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );


/**
 * Auto Complete all WooCommerce orders.
 */
function custom_woocommerce_auto_complete_order( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}
add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );


function blythe_download_only_func() {
    return '<p style="color: red;">This item is available via download only</p>';
}
add_shortcode('blythe_download_only', 'blythe_download_only_func');


function blythe_wc_product_tabs($tabs = array()) {
    unset($tabs['reviews']);
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'blythe_wc_product_tabs');



function blythe_wc_filter_phone( $address_fields ) {
    $address_fields['billing_phone']['required'] = false;
    return $address_fields;
}
add_filter( 'woocommerce_billing_fields', 'blythe_wc_filter_phone', 10, 1 );


add_filter( 'manage_edit-shop_order_columns', 'blythe_woo_custom_column' );
function blythe_woo_custom_column( $columns ) {
    return array_slice( $columns, 0, 3, true )
        + array( 'blythe_woo_downloaded' => 'Downloaded' )
        + array_slice( $columns, 3, NULL, true );
}

add_action( 'manage_shop_order_posts_custom_column', 'blythe_woo_custom_column_value', 2 );
function blythe_woo_custom_column_value( $column ) {
    global $wpdb, $post, $blythe_woo_downloads;

    //start editing, I was saving my fields for the orders as custom post meta
    //if you did the same, follow this code

    if ( $column == 'blythe_woo_downloaded' ) {
        $order    = wc_get_order( $post->ID );

        if (!isset($blythe_woo_downloads)) {
            $blythe_woo_downloads = $wpdb->get_results( $wpdb->prepare( "
						SELECT order_id, SUM(download_count) AS dl FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
						GROUP BY order_id;
					", $post->ID ), OBJECT_K );
        }

        $value = (array)$blythe_woo_downloads[$post->ID];
        if ( !empty($value) ) {
            echo 0 == $value['dl'] ? 'Awaiting Download' : 'Downloaded';
        } else {
            echo 'N/A';
        }
    }
}

add_action('admin_head', 'blythe_woo_downloaded_css');

function blythe_woo_downloaded_css() {
    echo '<style>
	@media screen and (max-width: 782px) {
		.post-type-shop_order .wp-list-table td.blythe_woo_downloaded {
			float: right;
			display: inline-block !important;
			padding: 0 1em 1em 1em !important;
		}
		.post-type-shop_order .wp-list-table td.blythe_woo_downloaded::before {
			display: none !important;
		}
	}</style>';
}


function blythe_custom_action() {
    if ( is_product_category( 'sheet-music' )) {
        echo '<div class="sheet-music-request-wrapper">' .
            esc_html("Can't find the sheet music you're looking for?") .
            '<a class="button" href="https://www.theblythefamily.net/sheet-music-request/">Submit A Request</a>' .
            '</div>';
    }
}
add_action( 'woocommerce_after_main_content', 'blythe_custom_action', 15 );


//------------------------------------
//* END Woo-Commerce Code
//------------------------------------