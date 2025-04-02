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
 *
 * This is the main plugin file for Calculogic. It initializes the plugin, registers activation and deactivation hooks,
 * loads necessary files, and enqueues assets. This file acts as the entry point for the plugin.
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly to ensure security
}

/**
 * Plugin Activation Hook
 *
 * This function runs when the plugin is activated. It is used to perform setup tasks such as creating database tables,
 * setting default options, or initializing plugin-specific settings.
 *
 * Example: Adds an option to the WordPress database to indicate the plugin has been activated.
 */
function calculogic_activate() {
    // Ensure the current user has permission to activate plugins
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    // Example: Add a flag to the database to indicate the plugin was activated
    add_option( 'calculogic_plugin_activated', true );
}
register_activation_hook( __FILE__, 'calculogic_activate' );

/**
 * Plugin Deactivation Hook
 *
 * This function runs when the plugin is deactivated. It is used to clean up temporary data or reset settings.
 *
 * Example: Removes the activation flag from the WordPress database.
 */
function calculogic_deactivate() {
    // Ensure the current user has permission to deactivate plugins
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    // Example: Remove the activation flag from the database
    delete_option( 'calculogic_plugin_activated' );
}
register_deactivation_hook( __FILE__, 'calculogic_deactivate' );

/**
 * Plugin Initialization
 *
 * This function initializes the plugin by loading text domains for translations and including necessary files.
 * It is hooked to the `plugins_loaded` action to ensure all plugins are fully loaded before this plugin runs.
 */
function calculogic_init() {
    // Load the plugin's text domain for translations
    // This allows the plugin to support multiple languages
    load_plugin_textdomain( 'calculogic', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // Include necessary files for the plugin's functionality
    // BuddyPress Integration: Adds a custom dashboard tab to BuddyPress profiles
    if ( function_exists( 'bp_core_new_nav_item' ) ) {
        require_once plugin_dir_path( __FILE__ ) . 'includes/buddyPress-integration.php';
    }

    // Custom Post Types: Registers the unified "Types" CPT and "Configurations" CPT
    require_once plugin_dir_path( __FILE__ ) . 'includes/custom-post-types.php';

    // Helper Functions: Provides utility functions used throughout the plugin
    require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

    // Template Loader: Ensures custom templates are used for the plugin's post types
    require_once plugin_dir_path( __FILE__ ) . 'includes/template-loader.php';
}
add_action( 'plugins_loaded', 'calculogic_init' );

/**
 * Enqueue Scripts and Styles
 *
 * This function enqueues the plugin's CSS and JavaScript files. These assets are loaded on the front end of the site
 * to provide styling and interactivity for the plugin's features.
 */
function calculogic_enqueue_assets() {
    // Enqueue the plugin's main CSS file for styling
    wp_enqueue_style( 'calculogic-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );

    // Enqueue the plugin's main JavaScript file for interactivity
    // The script depends on jQuery and is loaded in the footer
    wp_enqueue_script( 'calculogic-script', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array( 'jquery' ), null, true );
}
add_action( 'wp_enqueue_scripts', 'calculogic_enqueue_assets' );
?>