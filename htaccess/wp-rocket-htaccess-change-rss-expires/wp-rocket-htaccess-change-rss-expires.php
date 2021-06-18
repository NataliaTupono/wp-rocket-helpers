<?php
/**
 * Plugin Name: WP Rocket | Change RSS Expires
 * Description: Disables the browser caching for RSS
 * Plugin URI:  https://github.com/wp-media/wp-rocket-helpers/tree/master/htaccess/wp-rocket-htaccess-change-rss-expires
 * Author:      WP Rocket Support Team
 * Author URI:  http://wp-rocket.me/
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright SAS WP MEDIA 2018
 */

namespace WP_Rocket\Helpers\htaccess\custom_rules;

// Standard plugin security, keep this line in place.
defined( 'ABSPATH' ) or die();

/**
 * Replaces the browser caching for RSS
 *
 * @author Vasilis Manthos
 *
 */
function rocket_change_rss_expires( $rules ){
	$rules_to_replace = array(  'ExpiresByType application/rss+xml           "access plus 1 hour"' => 'ExpiresByType application/rss+xml           "access plus 0 seconds"', // old rule => new rule
	'ExpiresByType application/atom+xml          "access plus 1 hour"' => 'ExpiresByType application/atom+xml          "access plus 0 seconds"',// old rule => new rule
							);
	foreach( $rules_to_replace as $old_rule=>$new_rule ){
		$rules = str_replace( $old_rule, $new_rule, $rules );
	}
	return $rules;
}
add_filter( 'rocket_htaccess_mod_expires', __NAMESPACE__ '\rocket_change_rss_expires' );

/**
 * Updates .htaccess, regenerates WP Rocket config file.
 *
 * @author Caspar Hübinger
 */
function flush_wp_rocket() {

	if ( ! function_exists( 'flush_rocket_htaccess' )
	  || ! function_exists( 'rocket_generate_config_file' ) ) {
		return false;
	}

	// Update WP Rocket .htaccess rules.
	flush_rocket_htaccess();

	// Regenerate WP Rocket config file.
	rocket_generate_config_file();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\flush_wp_rocket' );

/**
 * Removes customizations, updates .htaccess, regenerates config file.
 *
 * @author Caspar Hübinger
 */
function deactivate() {

	// Remove all functionality added above. Please remove the correct filter.
	// remove_filter( 'before_rocket_htaccess_rules', __NAMESPACE__ . '\render_rewrite_rules' );
	remove_filter( 'after_rocket_htaccess_rules', __NAMESPACE__ . '\render_rewrite_rules' );

	// Flush .htaccess rules, and regenerate WP Rocket config file.
	flush_wp_rocket();
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate' );
