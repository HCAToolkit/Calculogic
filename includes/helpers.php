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
    error_log( 'AJAX Request: ' . print_r( $_POST, true ) );

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
 * AJAX handler to create or update an item.
 */
function calculogic_save_item_settings() {
    error_log( 'AJAX Request: ' . print_r( $_POST, true ) );

    // Verify nonce for security
    if ( ! check_ajax_referer( 'calculogic_nonce', 'nonce', false ) ) {
        wp_send_json_error( __( 'Invalid nonce. Please refresh the page and try again.', 'calculogic' ) );
    }

    $post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
    $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';

    if ( empty( $title ) || empty( $type ) ) {
        wp_send_json_error( __( 'Title and type are required.', 'calculogic' ) );
    }

    if ( $post_id ) {
        // Update existing item
        $updated = wp_update_post( array(
            'ID'         => $post_id,
            'post_title' => $title,
        ) );

        if ( is_wp_error( $updated ) || $updated === 0 ) {
            wp_send_json_error( __( 'Failed to update item.', 'calculogic' ) );
        }

        update_post_meta( $post_id, 'calculogic_item_type', $type );
    } else {
        // Create new item
        $post_id = wp_insert_post( array(
            'post_title'  => $title,
            'post_type'   => 'calculogic_type',
            'post_status' => 'publish',
        ) );

        if ( is_wp_error( $post_id ) ) {
            error_log( 'Failed to create item: ' . $post_id->get_error_message() );
            wp_send_json_error( __( 'Failed to create item.', 'calculogic' ) );
        }

        update_post_meta( $post_id, 'calculogic_item_type', $type );
    }

    wp_send_json_success( array( 'id' => $post_id, 'title' => $title, 'type' => $type ) );
}
add_action( 'wp_ajax_create_calculogic_item', 'calculogic_save_item_settings' );
add_action( 'wp_ajax_update_calculogic_item', 'calculogic_save_item_settings' );

/**
 * AJAX handler to delete an item.
 */
function calculogic_delete_item() {
    error_log( 'AJAX Request: ' . print_r( $_POST, true ) );

    // Verify nonce for security
    if ( ! check_ajax_referer( 'calculogic_nonce', 'nonce', false ) ) {
        wp_send_json_error( __( 'Invalid nonce. Please refresh the page and try again.', 'calculogic' ) );
    }

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
    error_log( 'AJAX Request: ' . print_r( $_POST, true ) );

    // Verify nonce for security
    if ( ! check_ajax_referer( 'calculogic_nonce', 'nonce', false ) ) {
        wp_send_json_error( __( 'Invalid nonce. Please refresh the page and try again.', 'calculogic' ) );
    }

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