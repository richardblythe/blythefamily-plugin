<?php
//------------------------------------
//* Woo-Commerce Code
//------------------------------------
update_option( 'woocommerce_prepend_shop_page_to_urls', 'yes' );
add_theme_support( 'genesis-connect-woocommerce' );
add_filter( 'loop_shop_per_page', function() { return 6; }, 20 );


//Hide Price Range for WooCommerce Variable Products
//https://learnwoo.com/hide-price-range-woocommerce-variable-products/
add_filter( 'woocommerce_variable_sale_price_html',
	'lw_variable_product_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html',
	'lw_variable_product_price', 10, 2 );

function lw_variable_product_price( $v_price, $v_product ) {

// Product Price
	$prod_prices = array( $v_product->get_variation_price( 'min', true ),
		$v_product->get_variation_price( 'max', true ) );
	$prod_price = $prod_prices[0]!==$prod_prices[1] ? sprintf(__('From: %1$s', 'woocommerce'),
		wc_price( $prod_prices[0] ) ) : wc_price( $prod_prices[0] );

// Regular Price
	$regular_prices = array( $v_product->get_variation_regular_price( 'min', true ),
		$v_product->get_variation_regular_price( 'max', true ) );
	sort( $regular_prices );
	$regular_price = $regular_prices[0]!==$regular_prices[1] ? sprintf(__('From: %1$s','woocommerce')
		, wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );

	if ( $prod_price !== $regular_price ) {
		$prod_price = '<del>'.$regular_price.$v_product->get_price_suffix() . '</del> <ins>' .
		              $prod_price . $v_product->get_price_suffix() . '</ins>';
	}
	return $prod_price;
}

//Hide “From:$X”
add_filter('woocommerce_get_price_html', 'lw_hide_variation_price', 10, 2);
function lw_hide_variation_price( $v_price, $v_product ) {
	$v_product_types = array( 'variable');
	if ( in_array ( $v_product->product_type, $v_product_types ) && !(is_shop()) ) {
		return '';
	}
// return regular price
	return $v_price;
}



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
        + array( 'blythe_delivery_status' => 'Delivery Status' )
        + array_slice( $columns, 3, NULL, true );
}

add_action( 'manage_shop_order_posts_custom_column', 'blythe_woo_custom_column_value', 2 );
function blythe_woo_custom_column_value( $column ) {
    global $wpdb, $post, $blythe_woo_downloads;


    if ( $column == 'blythe_delivery_status' ) {

	    $order = wc_get_order( $post->ID );
	    $download_status = null;
	    $shipping_status = null;

    	$order_resolved = ( $order->get_status() == 'refunded' ) ||
	         get_post_meta($post->ID, 'blythe_woo_order_resolved', true );

    	if ( ! $order_resolved ) {

		    //--------------------------------
		    //       Shipping Status
		    //--------------------------------
		    $shipping_methods = $order->get_shipping_methods();
		    if ( is_array( $shipping_methods ) && count( $shipping_methods ) ) {
			    $provider = 'usps'; //todo Add other shipping providers?
			    $tracking = get_post_meta( $post->ID, 'blythe_woo_shipping_provider_tracking', true );
			    if ( $tracking ) {
				    $tracking_url = '';
				    if ( 'usps' == $provider ) {
					    $tracking_url = "https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1={$tracking}";
				    }
				    $shipping_status = "<a href='{$tracking_url}' target='_blank' >Shipped</a>";
			    } else {
				    $shipping_status = '<strong>Ship</strong>';
			    }
		    }


		    //---------------------------------
		    //     Global Downloads Variable
		    if ( ! isset( $blythe_woo_downloads ) ) {
			    $blythe_woo_downloads = $wpdb->get_results( "
						SELECT order_id, SUM(download_count) AS dl FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
						GROUP BY order_id;
					", OBJECT_K );
		    }


		    ///----------------------------------
		    //      Download Status

		    if ( isset( $blythe_woo_downloads[ $post->ID ] ) ) {
			    $value           = (array) $blythe_woo_downloads[ $post->ID ];
			    $download_status = ( 0 == $value['dl'] ? '<strong>DL Pending</strong>' : 'Downloaded' );
		    }
	    }

        //----------------------------------
	    //Column Display
	    if ( $order_resolved ) {
	    	echo 'Resolved';
	    } else if ( $download_status ) {
		    echo $download_status . ( $shipping_status ? " / {$shipping_status}" : '' );
	    } else {
	    	echo $shipping_status;
	    }

    }
}

add_action('admin_head', 'blythe_delivery_status_css');

function blythe_delivery_status_css() {
    echo '<style>
	@media screen and (max-width: 782px) {
		.post-type-shop_order .wp-list-table td.blythe_delivery_status {
			float: right;
			display: inline-block !important;
			padding: 0 1em 1em 1em !important;
		}
		.post-type-shop_order .wp-list-table td.blythe_delivery_status::before {
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