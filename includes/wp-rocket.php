<?php
function blythe_wprocket_rewrite_rules( $marker ) {

	$redirection = '# Redirect www to non-www' . PHP_EOL;
	$redirection .= 'RewriteEngine On' . PHP_EOL;

	//// EDIT THESE 2 LINES ////
	$redirection .= 'RewriteCond %{HTTP_HOST} ^www.blythefamily\.com [NC]' . PHP_EOL;
	$redirection .= 'RewriteRule ^(.*)$ https://blythefamily.com/$1 [L,R=301]' . PHP_EOL . PHP_EOL;
	//// STOP EDITING ////

	// Prepend redirection rules to WP Rocket block.
	$marker = $redirection . $marker;

	return $marker;
}
add_filter( 'before_rocket_htaccess_rules', 'blythe_wprocket_rewrite_rules' );


/**
 * Updates .htaccess, regenerates WP Rocket config file.
 *
 * @author Caspar Hübinger
 */
function blythe_wprocket_activate() {

	if ( ! function_exists( 'flush_rocket_htaccess' )
	     || ! function_exists( 'rocket_generate_config_file' ) ) {
		return false;
	}

	// Update WP Rocket .htaccess rules.
	flush_rocket_htaccess();

	// Regenerate WP Rocket config file.
	rocket_generate_config_file();
}


/**
 * Removes customizations, updates .htaccess, regenerates config file.
 *
 * @author Caspar Hübinger
 */
function blythe_wprocket_deactivate() {

	if ( function_exists( 'flush_wp_rocket' ) ) {

		// Remove all functionality added above.
		remove_filter( 'before_rocket_htaccess_rules', 'blythe_wprocket_rewrite_rules' );

		// Flush .htaccess rules, and regenerate WP Rocket config file.
		flush_wp_rocket();
	}
}