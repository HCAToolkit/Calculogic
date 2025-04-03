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
?>