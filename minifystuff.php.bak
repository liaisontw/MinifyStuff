<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/liaisontw/MinifyStuff
 * @since             1.0.0
 * @package           minifyStuff
 *
 * @wordpress-plugin
 * Plugin Name:       Minify Stuff
 * Plugin URI:        https://github.com/liaisontw/MinifyStuff
 * Description:       Minify HTML CSS javascript files
 * Version:           1.0.0
 * Author:            Liaison Chang
 * Author URI:        https://github.com/liaisontw//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       minifystuff
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'minifyStuff_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-minifyStuff-activator.php
 */
function activate_minifyStuff() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-minifyStuff-activator.php';
	minifyStuff_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-minifyStuff-deactivator.php
 */
function deactivate_minifyStuff() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-minifyStuff-deactivator.php';
	minifyStuff_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_minifyStuff' );
register_deactivation_hook( __FILE__, 'deactivate_minifyStuff' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-minifyStuff.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_minifyStuff() {

	$plugin = new minifyStuff();
	$plugin->run();

}
run_minifyStuff();
