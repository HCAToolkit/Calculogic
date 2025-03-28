<?php
/**
 * Plugin Name: Calculogic: Advanced Templates and Quizzes for Creative Minds and Creators
 * Plugin URI: https://www.hcatoolkit.com
 * Description: A WordPress plugin for creating advanced templates and quizzes tailored for creative minds and creators.
 * Version: 1.0.0
 * Author: Yuri Bara
 * Author URI: https://www.hcatoolkit.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: calculogic
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Include BuddyPress integration
require_once plugin_dir_path( __FILE__ ) . 'includes/buddyPress-integration.php';

// Plugin activation hook
function calculogic_activate() {
    // Code to run on plugin activation (e.g., create database tables, set default options)
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
    // Example: Add an option to the database
    add_option( 'calculogic_plugin_activated', true );
}
register_activation_hook( __FILE__, 'calculogic_activate' );

// Plugin deactivation hook
function calculogic_deactivate() {
    // Code to run on plugin deactivation (e.g., clean up temporary data)
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
    // Example: Remove the option from the database
    delete_option( 'calculogic_plugin_activated' );
}
register_deactivation_hook( __FILE__, 'calculogic_deactivate' );

// Plugin initialization
function calculogic_init() {
    // Code to initialize the plugin (e.g., load text domain, enqueue scripts)
    load_plugin_textdomain( 'calculogic', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'calculogic_init' );