<?php
/**
 * Helper functions for Calculogic
 *
 * This file contains various helper functions that assist with tasks
 * throughout the Calculogic plugin, such as sanitizing input, formatting
 * output, and querying custom post types.
 *
 * @package Calculogic
 */

/**
 * Sanitize a string input.
 *
 * @param string $input The input string to sanitize.
 * @return string The sanitized string.
 */
function calculogic_sanitize_string( $input ) {
    return sanitize_text_field( $input );
}

/**
 * Format a date for display.
 *
 * @param string $date The date string to format.
 * @return string The formatted date.
 */
function calculogic_format_date( $date ) {
    return date_i18n( get_option( 'date_format' ), strtotime( $date ) );
}

/**
 * Query custom post types.
 *
 * @param string $post_type The post type to query.
 * @param array $args Optional. Additional query arguments.
 * @return WP_Query The query object.
 */
function calculogic_query_post_type( $post_type, $args = array() ) {
    $default_args = array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
    );
    $query_args = wp_parse_args( $args, $default_args );
    return new WP_Query( $query_args );
}

/**
 * AJAX handler to load CPT items.
 */
function calculogic_load_items() {
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : 'all';

    $args = array(
        'post_type' => $filter === 'all' ? array('calculator', 'quiz', 'template') : $filter,
        's' => $search,
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="calculogic-item">';
            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
            echo '<p>' . esc_html(get_the_excerpt()) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>' . __('No items found.', 'calculogic') . '</p>';
    }

    wp_die();
}
add_action('wp_ajax_load_calculogic_items', 'calculogic_load_items');
add_action('wp_ajax_nopriv_load_calculogic_items', 'calculogic_load_items');

/**
 * AJAX handler to create a new item.
 */
function calculogic_create_item() {
    // Verify nonce for security
    check_ajax_referer( 'calculogic_nonce', 'nonce' );

    $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
    $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';

    if ( empty( $title ) || empty( $type ) ) {
        wp_send_json_error( __( 'Title and type are required.', 'calculogic' ) );
    }

    $post_id = wp_insert_post( array(
        'post_title'  => $title,
        'post_type'   => $type,
        'post_status' => 'publish',
    ) );

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( __( 'Failed to create item.', 'calculogic' ) );
    }

    wp_send_json_success( array( 'id' => $post_id, 'title' => $title ) );
}
add_action( 'wp_ajax_create_calculogic_item', 'calculogic_create_item' );

/**
 * AJAX handler to update an item.
 */
function calculogic_update_item() {
    // Verify nonce for security
    check_ajax_referer( 'calculogic_nonce', 'nonce' );

    $post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    if ( empty( $post_id ) || empty( $title ) ) {
        wp_send_json_error( __( 'ID and title are required.', 'calculogic' ) );
    }

    $updated = wp_update_post( array(
        'ID'         => $post_id,
        'post_title' => $title,
    ) );

    if ( is_wp_error( $updated ) || $updated === 0 ) {
        wp_send_json_error( __( 'Failed to update item.', 'calculogic' ) );
    }

    wp_send_json_success( array( 'id' => $post_id, 'title' => $title ) );
}
add_action( 'wp_ajax_update_calculogic_item', 'calculogic_update_item' );

/**
 * AJAX handler to delete an item.
 */
function calculogic_delete_item() {
    // Verify nonce for security
    check_ajax_referer( 'calculogic_nonce', 'nonce' );

    $post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

    if ( empty( $post_id ) ) {
        wp_send_json_error( __( 'ID is required.', 'calculogic' ) );
    }

    $deleted = wp_delete_post( $post_id, true );

    if ( ! $deleted ) {
        wp_send_json_error( __( 'Failed to delete item.', 'calculogic' ) );
    }

    wp_send_json_success( array( 'id' => $post_id ) );
}
add_action( 'wp_ajax_delete_calculogic_item', 'calculogic_delete_item' );

/**
 * AJAX handler to read an item.
 */
function calculogic_read_item() {
    // Verify nonce for security
    check_ajax_referer( 'calculogic_nonce', 'nonce' );

    $post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

    if ( empty( $post_id ) ) {
        wp_send_json_error( __( 'ID is required.', 'calculogic' ) );
    }

    $post = get_post( $post_id );

    if ( ! $post || $post->post_status !== 'publish' ) {
        wp_send_json_error( __( 'Item not found.', 'calculogic' ) );
    }

    wp_send_json_success( array(
        'id'      => $post->ID,
        'title'   => $post->post_title,
        'content' => $post->post_content,
    ) );
}
add_action( 'wp_ajax_read_calculogic_item', 'calculogic_read_item' );
?>