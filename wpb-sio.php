<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://wpbuild.ru
 * @since             1.0.0
 * @package           Wpb_Sio
 *
 * @wordpress-plugin
 * Plugin Name:       WPB SEO Images Optimized
 * Plugin URI:        http://wpbuild.ru
 * Description:       Заставьте меня сделать описание.
 * Version:           1.0.3
 * Author:            WPBbuild
 * Author URI:        http://wpbuild.ru
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpb-sio
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
define( 'WPB_SIO_VERSION', '1.0.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpb-sio-activator.php
 */
function activate_wpb_sio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpb-sio-activator.php';
	Wpb_Sio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpb-sio-deactivator.php
 */
function deactivate_wpb_sio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpb-sio-deactivator.php';
	Wpb_Sio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpb_sio' );
register_deactivation_hook( __FILE__, 'deactivate_wpb_sio' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpb-sio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpb_sio() {

	$plugin = new Wpb_Sio();
	$plugin->run();

}
run_wpb_sio();
