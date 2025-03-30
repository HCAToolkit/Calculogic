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
?>