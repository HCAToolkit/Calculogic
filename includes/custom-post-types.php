<?php
/**
 * Custom Post Types for Calculogic
 *
 * This file registers custom post types for managing templates, quizzes, and calculators.
 *
 * @package Calculogic
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register Custom Post Types for Configurations and Types.
 */
function calculogic_register_post_types() {
    // Configurations CPT
    $labels_config = array(
        'name'                  => _x( 'Calculogic Configurations', 'Post Type General Name', 'calculogic' ),
        'singular_name'         => _x( 'Calculogic Configuration', 'Post Type Singular Name', 'calculogic' ),
        'menu_name'             => __( 'Configurations', 'calculogic' ),
        'name_admin_bar'        => __( 'Configuration', 'calculogic' ),
        'add_new_item'          => __( 'Add New Configuration', 'calculogic' ),
        'edit_item'             => __( 'Edit Configuration', 'calculogic' ),
        'view_item'             => __( 'View Configuration', 'calculogic' ),
        'all_items'             => __( 'All Configurations', 'calculogic' ),
        'search_items'          => __( 'Search Configurations', 'calculogic' ),
        'not_found'             => __( 'No configurations found.', 'calculogic' ),
        'not_found_in_trash'    => __( 'No configurations found in Trash.', 'calculogic' ),
    );
    
    $args_config = array(
        'labels'                => $labels_config,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'calculogic-config' ),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => null,
        'supports'              => array( 'title', 'editor' ),
        'show_in_rest'          => true,
    );
    register_post_type( 'calculogic_config', $args_config );
    
    // Types CPT – a container for your projects (templates, quizzes, etc.)
    $labels_type = array(
        'name'                  => _x( 'Calculogic Types', 'Post Type General Name', 'calculogic' ),
        'singular_name'         => _x( 'Calculogic Type', 'Post Type Singular Name', 'calculogic' ),
        'menu_name'             => __( 'Types', 'calculogic' ),
        'name_admin_bar'        => __( 'Type', 'calculogic' ),
        'add_new_item'          => __( 'Add New Type', 'calculogic' ),
        'edit_item'             => __( 'Edit Type', 'calculogic' ),
        'view_item'             => __( 'View Type', 'calculogic' ),
        'all_items'             => __( 'All Types', 'calculogic' ),
        'search_items'          => __( 'Search Types', 'calculogic' ),
        'not_found'             => __( 'No types found.', 'calculogic' ),
        'not_found_in_trash'    => __( 'No types found in Trash.', 'calculogic' ),
    );
    
    $args_type = array(
        'labels'                => $labels_type,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'calculogic-type' ),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => null,
        'supports'              => array( 'title', 'editor' ),
        'show_in_rest'          => true,
    );
    register_post_type( 'calculogic_type', $args_type );
}
add_action( 'init', 'calculogic_register_post_types' );

/**
 * AJAX Endpoint: Toggle Favorites
 */
function calculogic_favorite() {
    // Verify nonce for security
    check_ajax_referer( 'calculogic_nonce', 'nonce' );
    
    // Ensure user is logged in
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( 'Not logged in' );
    }
    
    // Get configuration ID from AJAX request
    $config_id = intval( $_POST['config_id'] );
    if ( ! $config_id ) {
        wp_send_json_error( 'Invalid configuration ID' );
    }
    
    // Retrieve current favorites from user meta
    $favorites = get_user_meta( $user_id, 'calculogic_favorites', true );
    if ( ! is_array( $favorites ) ) {
        $favorites = array();
    }
    
    // Toggle favorite: add if not present; remove if already favorited
    if ( in_array( $config_id, $favorites ) ) {
        $favorites = array_diff( $favorites, array( $config_id ) );
        $action = 'removed';
    } else {
        $favorites[] = $config_id;
        $action = 'added';
    }
    
    update_user_meta( $user_id, 'calculogic_favorites', $favorites );
    wp_send_json_success( array( 'action' => $action, 'favorites' => $favorites ) );
}
add_action( 'wp_ajax_calculogic_favorite', 'calculogic_favorite' );
add_action( 'wp_ajax_nopriv_calculogic_favorite', 'calculogic_favorite' );

/**
 * AJAX Endpoint: Nominate a Configuration
 */
function calculogic_nominate() {
    // Verify nonce for security
    check_ajax_referer( 'calculogic_nonce', 'nonce' );
    
    // Ensure user is logged in
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( 'Not logged in' );
    }
    
    // Get configuration ID from AJAX request
    $config_id = intval( $_POST['config_id'] );
    if ( ! $config_id ) {
        wp_send_json_error( 'Invalid configuration ID' );
    }
    
    // Prevent duplicate nominations
    $nominated_users = get_post_meta( $config_id, 'nominated_users', true );
    if ( ! is_array( $nominated_users ) ) {
        $nominated_users = array();
    }
    if ( in_array( $user_id, $nominated_users ) ) {
        wp_send_json_error( 'Already nominated' );
    }
    
    // Add user to nominated list and increment nomination count
    $nominated_users[] = $user_id;
    update_post_meta( $config_id, 'nominated_users', $nominated_users );
    
    $nomination_count = intval( get_post_meta( $config_id, 'nomination_count', true ) );
    $nomination_count++;
    update_post_meta( $config_id, 'nomination_count', $nomination_count );
    
    wp_send_json_success( array( 'nomination_count' => $nomination_count ) );
}
add_action( 'wp_ajax_calculogic_nominate', 'calculogic_nominate' );
add_action( 'wp_ajax_nopriv_calculogic_nominate', 'calculogic_nominate' );

/**
 * Enqueue Scripts and Styles
 */
function calculogic_enqueue_assets() {
    // Enqueue JS and CSS files (adjust paths if needed)
    wp_enqueue_script( 'calculogic-script', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array('jquery'), '1.0.0', true );
    wp_enqueue_style( 'calculogic-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );
    
    // Localize script with AJAX URL and nonce
    wp_localize_script( 'calculogic-script', 'calculogic', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'calculogic_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'calculogic_enqueue_assets' );
add_action( 'admin_enqueue_scripts', 'calculogic_enqueue_assets' );