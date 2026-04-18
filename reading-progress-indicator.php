<?php
/**
 * Plugin Name: Reading Progress Indicator
 * Plugin URI:  https://github.com/mademi5/reading-progress-indicator
 * Description: A customizable animated reading progress bar for WordPress
 * Version:     1.0.0
 * Author:      Merve
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: reading-progress-indicator
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package ReadingProgressIndicator
 */

defined( 'ABSPATH' ) || exit;

define( 'RPI_VERSION', '1.0.0' );
define( 'RPI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RPI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RPI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Return default plugin settings.
 *
 * @return array Associative array of default option values.
 */
function rpi_get_defaults() {
	return array(
		'enabled'            => 1,
		'position'           => 'top',
		'style'              => 'linear',
		'color'              => '#3B82F6',
		'gradient_end_color' => '#8B5CF6',
		'thickness'          => 4,
		'border_radius'      => 0,
		'opacity'            => 1.0,
		'animation'          => 'smooth',
		'show_percentage'    => 0,
		'show_congrats'      => 1,
		'display_on'         => array( 'post' ),
	);
}

/**
 * Retrieve merged plugin settings.
 *
 * @return array Current settings merged with defaults.
 */
function rpi_get_settings() {
	$defaults = rpi_get_defaults();
	$saved    = get_option( 'rpi_settings', array() );

	return wp_parse_args( $saved, $defaults );
}

require_once RPI_PLUGIN_DIR . 'includes/settings.php';
require_once RPI_PLUGIN_DIR . 'includes/enqueue.php';

/**
 * Add a "Settings" link on the plugins list page.
 *
 * @param array $links Existing action links.
 * @return array Modified action links.
 */
function rpi_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=reading-progress' ) ),
		esc_html__( 'Settings', 'reading-progress-indicator' )
	);
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links_' . RPI_PLUGIN_BASENAME, 'rpi_plugin_action_links' );

/**
 * Set default options on plugin activation.
 *
 * @return void
 */
function rpi_activate() {
	if ( false === get_option( 'rpi_settings' ) ) {
		add_option( 'rpi_settings', rpi_get_defaults() );
	}
}
register_activation_hook( __FILE__, 'rpi_activate' );
